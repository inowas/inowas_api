<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Inowas\Common\Boundaries\Area;
use Inowas\Common\Boundaries\BoundaryName;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Geometry\Polygon;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\Modelname;
use Inowas\Common\Modflow\ModflowModelDescription;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Common\Soilmodel\SoilmodelId;
use Inowas\ModflowBundle\Exception\NotFoundException;
use Inowas\ModflowModel\Model\Command\ChangeModflowModelBoundingBox;
use Inowas\ModflowModel\Model\Command\ChangeModflowModelDescription;
use Inowas\ModflowModel\Model\Command\ChangeModflowModelGridSize;
use Inowas\ModflowModel\Model\Command\ChangeModflowModelName;
use Inowas\ModflowModel\Model\Command\ChangeModflowModelSoilmodelId;
use Inowas\ModflowModel\Model\Command\CreateModflowModel;
use Inowas\ModflowModel\Model\Command\UpdateAreaGeometry;
use Inowas\ModflowModel\Model\Command\UpdateActiveCells;
use Inowas\ModflowModel\Model\Command\UpdateLengthUnit;
use Inowas\ModflowModel\Model\Command\UpdateTimeUnit;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ModflowModelController extends InowasRestController
{
    /**
     * Get list of modflow models from user
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get list of modflow models from user.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Rest\Get("/modflowmodels")
     * @return JsonResponse
     */
    public function getModflowModelsAction(): JsonResponse
    {
        return new JsonResponse(
            $this->get('inowas.modflowmodel.model_finder')->findModelsByBaseUserId($this->getUserId())
        );
    }

    /**
     * Get list of all public modflow models
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get list of my Modflow Models.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Rest\Get("/modflowmodels/public")
     * @return JsonResponse
     */
    public function getPublicModflowModelsAction(): JsonResponse
    {
        $this->assertUserIsLoggedInCorrectly();

        return new JsonResponse(
            $this->get('inowas.modflowmodel.model_finder')->findPublicModels()
        );
    }

    /**
     * Create and add a new modflow model
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create and add a new modflow model.",
     *   statusCodes = {
     *     302 = "Redirect to the model"
     *   }
     * )
     *
     * @Rest\Post("/modflowmodels")
     * @param Request $request
     * @return RedirectResponse
     */
    public function postModflowModelsAction(Request $request): RedirectResponse
    {
        $content = $this->getContentAsArray($request);

        $userId = $this->getUserId();
        $modelId = ModflowId::generate();

        $this->assertContainsKey('name', $content);
        $name = Modelname::fromString($content['name']);

        $this->assertContainsKey('description', $content);
        $description = ModflowModelDescription::fromString($content['description']);

        $this->assertContainsKey('area_geometry', $content);
        $areaGeometry = new Polygon($content['area_geometry']['coordinates'], 4326);
        $area = Area::create(BoundaryId::generate(), BoundaryName::fromString($name->toString().' Area'), $areaGeometry);

        $this->assertContainsKey('grid_size', $content);
        $gridSize = GridSize::fromXY((int)$content['grid_size']['n_x'], (int)$content['grid_size']['n_y']);

        $this->assertContainsKey('time_unit', $content);
        $timeUnit = TimeUnit::fromInt((int)$content['time_unit']);

        $this->assertContainsKey('length_unit', $content);
        $lengthUnit = LengthUnit::fromInt((int)$content['length_unit']);

        $commandBus = $this->get('prooph_service_bus.modflow_command_bus');
        $commandBus->dispatch(CreateModflowModel::newWithIdAndUnits($userId, $modelId, $area, $gridSize, $timeUnit, $lengthUnit));
        $commandBus->dispatch(ChangeModflowModelName::forModflowModel($userId, $modelId, $name));
        $commandBus->dispatch(ChangeModflowModelDescription::forModflowModel($userId, $modelId, $description));

        return new RedirectResponse(
            $this->generateUrl('get_modflow_model', array('id' => $modelId->toString())),
            302
        );
    }

    /**
     * Get details of modflow model by id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get details of modflow model by id.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param string $id
     * @Rest\Get("/modflowmodels/{id}")
     * @return JsonResponse
     */
    public function getModflowModelAction(string $id): JsonResponse
    {
        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);
        $details = $this->get('inowas.modflowmodel.model_finder')->getModelDetailsByModelId($modelId);

        if (null === $details) {
            throw NotFoundException::withMessage(sprintf(
                'ModflowModel with id: \'%s\' not found.', $modelId->toString()
            ));
        }

        return new JsonResponse($details);
    }

    /**
     * Get name of modflow model by id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get name of modflow model by id.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param string $id
     * @Rest\Get("/modflowmodels/{id}/name")
     * @return JsonResponse
     */
    public function getModflowModelNameAction(string $id): JsonResponse
    {
        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);
        $name = $this->get('inowas.modflowmodel.model_finder')->getModelNameByModelId($modelId);

        if (! $name instanceof Modelname) {
            throw NotFoundException::withMessage(sprintf(
                'ModflowModel with id: \'%s\' not found.', $modelId->toString()
            ));
        }

        return new JsonResponse(['name' => $name->toString()]);
    }

    /**
     * Update name of modflow model by id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update name of modflow model by id.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param Request $request
     * @param string $id
     * @Rest\Put("/modflowmodels/{id}/name")
     * @return Response
     */
    public function putModflowModelNameAction(Request $request, string $id)
    {
        $userId = $this->getUserId();

        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);

        $content = $this->getContentAsArray($request);
        $this->assertContainsKey('name', $content);
        $name = Modelname::fromString($content['name']);

        $this->get('prooph_service_bus.modflow_command_bus')->dispatch(ChangeModflowModelName::forModflowModel($userId, $modelId, $name));

        return new RedirectResponse(
            $this->generateUrl('get_modflow_model', array('id' => $modelId->toString())),
            302
        );
    }

    /**
     * Get description of modflow model by id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get description of modflow model by id.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param string $id
     * @Rest\Get("/modflowmodels/{id}/description")
     * @return JsonResponse
     */
    public function getModflowModelDescriptionAction(string $id): JsonResponse
    {
        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);

        $description = $this->get('inowas.modflowmodel.model_finder')->getModelDescriptionByModelId($modelId);

        if (! $description instanceof ModflowModelDescription) {
            throw NotFoundException::withMessage(sprintf(
                'ModflowModel with id: \'%s\' not found.', $modelId->toString()
            ));
        }

        return new JsonResponse(['description' => $description->toString()]);
    }

    /**
     * Update description of modflow model by id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update description of modflow model by id.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param Request $request
     * @param string $id
     * @Rest\Put("/modflowmodels/{id}/description")
     * @return Response
     */
    public function putModflowModelDescriptionAction(Request $request, string $id)
    {
        $userId = $this->getUserId();

        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);

        $content = $this->getContentAsArray($request);
        $this->assertContainsKey('description', $content);
        $description = ModflowModelDescription::fromString($content['description']);

        $this->get('prooph_service_bus.modflow_command_bus')->dispatch(ChangeModflowModelDescription::forModflowModel($userId, $modelId, $description));

        $response = new RedirectResponse(
            $this->generateUrl('get_modflow_model_description', array('id' => $modelId->toString())),
            302
        );

        return $response;
    }

    /**
     * Get geometry of modflow model area by id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get geometry of modflow model area by id.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param string $id
     * @Rest\Get("/modflowmodels/{id}/geometry")
     * @return JsonResponse
     */
    public function getModflowModelGeometryAction(string $id): JsonResponse
    {
        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);
        $geometry = $this->get('inowas.modflowmodel.model_finder')->getAreaGeometryByModflowModelId($modelId);

        if (! $geometry instanceof Geometry) {
            throw NotFoundException::withMessage(sprintf(
                'ModflowModel with id: \'%s\' not found.', $modelId->toString()
            ));
        }

        return new JsonResponse(['geometry' => $geometry]);
    }

    /**
     * Update area geometry of modflow model by id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update area geometry of modflow model by id.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param Request $request
     * @param string $id
     * @Rest\Put("/modflowmodels/{id}/geometry")
     * @return Response
     */
    public function putModflowModelGeometryAction(Request $request, string $id)
    {
        $userId = $this->getUserId();

        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);

        $content = $this->getContentAsArray($request);
        $this->assertContainsKey('geometry', $content);
        $this->assertGeometryIsValid($content['geometry']);

        $polygon = new Polygon($content['geometry']['coordinates']);
        $this->get('prooph_service_bus.modflow_command_bus')->dispatch(UpdateAreaGeometry::of($userId, $modelId, $polygon));

        $response = new RedirectResponse(
            $this->generateUrl('get_modflow_model_geometry', array('id' => $modelId->toString())),
            302
        );

        return $response;
    }

    /**
     * Get boundingBox of modflow model by id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get boundingBox of modflow model by id.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param string $id
     * @Rest\Get("/modflowmodels/{id}/boundingbox")
     * @return JsonResponse
     */
    public function getModflowModelBoundingBoxAction(string $id): JsonResponse
    {
        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);

        $boundingBox = $this->get('inowas.modflowmodel.model_finder')->getBoundingBoxByModflowModelId($modelId);

        if (! $boundingBox instanceof BoundingBox) {
            throw NotFoundException::withMessage(sprintf(
                'ModflowModel with id: \'%s\' not found.', $modelId->toString()
            ));
        }

        return new JsonResponse(['bounding_box' => $boundingBox]);
    }

    /**
     * Update boundingBox of modflow model by id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update boundingBox of modflow model by id.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param Request $request
     * @param string $id
     * @Rest\Put("/modflowmodels/{id}/boundingbox")
     * @return Response
     */
    public function putModflowModelBoundingBoxAction(Request $request, string $id)
    {
        $userId = $this->getUserId();

        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);

        $content = $this->getContentAsArray($request);
        $this->assertContainsKey('bounding_box', $content);

        $boundingBox = BoundingBox::fromCoordinates(
            $content['bounding_box']['x_min'],
            $content['bounding_box']['x_max'],
            $content['bounding_box']['y_min'],
            $content['bounding_box']['y_max'],
            4326,
            0,
            0
        );

        $this->get('prooph_service_bus.modflow_command_bus')->dispatch(ChangeModflowModelBoundingBox::forModflowModel($userId, $modelId, $boundingBox));

        $response = new RedirectResponse(
            $this->generateUrl('get_modflow_model_bounding_box', array('id' => $modelId->toString())),
            302
        );

        return $response;
    }

    /**
     * Get gridSize of modflow model by id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get gridSize of modflow model by id.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param string $id
     * @Rest\Get("/modflowmodels/{id}/gridsize")
     * @return JsonResponse
     */
    public function getModflowModelGridSizeAction(string $id): JsonResponse
    {
        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);
        $gridSize = $this->get('inowas.modflowmodel.model_finder')->getGridSizeByModflowModelId($modelId);

        if (! $gridSize instanceof GridSize) {
            throw NotFoundException::withMessage(sprintf(
                'ModflowModel with id: \'%s\' not found.', $modelId->toString()
            ));
        }

        return new JsonResponse(['grid_size' => $gridSize]);
    }

    /**
     * Update gridSize of modflow model by id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update gridSize of modflow model by id.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param Request $request
     * @param string $id
     * @Rest\Put("/modflowmodels/{id}/gridsize")
     * @return Response
     */
    public function putModflowModelGridSizeAction(Request $request, string $id)
    {
        $userId = $this->getUserId();
        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);

        $content = $this->getContentAsArray($request);
        $this->assertContainsKey('grid_size', $content);

        $gridSize = GridSize::fromXY($content['grid_size']['n_x'], $content['grid_size']['n_y']);
        $this->get('prooph_service_bus.modflow_command_bus')->dispatch(ChangeModflowModelGridSize::forModflowModel($userId, $modelId, $gridSize));

        $response = new RedirectResponse(
            $this->generateUrl('get_modflow_model_grid_size', array('id' => $modelId->toString())),
            302
        );

        return $response;
    }

    /**
     * Get ActiveCells of modflow model by id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get ActiveCells of modflow model by id.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param string $id
     * @Rest\Get("/modflowmodels/{id}/activecells")
     * @return JsonResponse
     */
    public function getModflowModelActiveCellsAction(string $id): JsonResponse
    {
        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);
        $activeCells = $this->get('inowas.modflowmodel.boundaries_finder')->findAreaActiveCells($modelId);

        if (! $activeCells instanceof ActiveCells) {
            throw NotFoundException::withMessage(sprintf(
                'ModflowModel with id: \'%s\' not found.', $modelId->toString()
            ));
        }

        return new JsonResponse(['active_cells' => $activeCells->fullArray()]);
    }

    /**
     * Update ActiveCells of modflow model by id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update ActiveCells of modflow model by id.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param Request $request
     * @param string $id
     * @Rest\Put("/modflowmodels/{id}/activecells")
     * @return Response
     */
    public function putModflowModelActiveCellsAction(Request $request, string $id)
    {
        $userId = $this->getUserId();
        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);

        $content = $this->getContentAsArray($request);
        $this->assertContainsKey('active_cells', $content);

        $activeCells = ActiveCells::fromFullArray($content['active_cells']);
        $this->get('prooph_service_bus.modflow_command_bus')->dispatch(UpdateActiveCells::ofModelArea($userId, $modelId, $activeCells));

        $response = new RedirectResponse(
            $this->generateUrl('get_modflow_model_active_cells', array('id' => $modelId->toString())),
            302
        );

        return $response;
    }

    /**
     * Get soilmodelId of modflow model by id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get soilmodelId of modflow model by id.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param string $id
     * @Rest\Get("/modflowmodels/{id}/soilmodel")
     * @return JsonResponse
     */
    public function getSoilModelIdAction(string $id): JsonResponse
    {
        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);
        $soilModelId = $this->get('inowas.modflowmodel.model_finder')->getSoilmodelIdByModelId($modelId);

        if (! $soilModelId instanceof SoilmodelId) {
            throw NotFoundException::withMessage(sprintf(
                'ModflowModel with id: \'%s\' not found.', $modelId->toString()
            ));
        }

        return new JsonResponse(['soilmodel_id' => $soilModelId->toString()]);
    }

    /**
     * Update soilmodelId of modflow model by id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update soilmodelId of modflow model by id.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param Request $request
     * @param string $id
     * @Rest\Put("/modflowmodels/{id}/soilmodel")
     * @return Response
     */
    public function putSoilModelIdAction(Request $request, string $id)
    {
        $userId = $this->getUserId();
        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);

        $content = $this->getContentAsArray($request);
        $this->assertContainsKey('soilmodel_id', $content);

        $soilmodelId = SoilmodelId::fromString($content['soilmodel_id']);
        $this->get('prooph_service_bus.modflow_command_bus')->dispatch(ChangeModflowModelSoilmodelId::forModflowModel($userId, $modelId, $soilmodelId));

        $response = new RedirectResponse(
            $this->generateUrl('get_soil_model_id', array('id' => $modelId->toString())),
            302
        );

        return $response;
    }

    /**
     * Get lengthUnit of modflow model by id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get lengthUnit of modflow model by id.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param string $id
     * @Rest\Get("/modflowmodels/{id}/lengthunit")
     * @return JsonResponse
     */
    public function getLengthUnitAction(string $id): JsonResponse
    {
        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);
        $lengthUnit = $this->get('inowas.modflowmodel.model_finder')->getLengthUnitByModelId($modelId);

        if (! $lengthUnit instanceof LengthUnit) {
            throw NotFoundException::withMessage(sprintf(
                'ModflowModel with id: \'%s\' not found.', $modelId->toString()
            ));
        }

        return new JsonResponse(['length_unit' => $lengthUnit->toInt()]);
    }

    /**
     * Update soilmodelId of modflow model by id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update soilmodelId of modflow model by id.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param Request $request
     * @param string $id
     * @Rest\Put("/modflowmodels/{id}/lengthunit")
     * @return Response
     */
    public function putLengthUnitAction(Request $request, string $id)
    {
        $userId = $this->getUserId();

        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);

        $content = $this->getContentAsArray($request);
        $this->assertContainsKey('length_unit', $content);

        $lengthUnit = LengthUnit::fromInt((int)$content['length_unit']);

        $this->get('prooph_service_bus.modflow_command_bus')->dispatch(UpdateLengthUnit::byUserAndModel($userId, $modelId, $lengthUnit));

        $response = new RedirectResponse(
            $this->generateUrl('get_length_unit', array('id' => $modelId->toString())),
            302
        );

        return $response;
    }

    /**
     * Get soilmodelId of modflow model by id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get soilmodelId of modflow model by id.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param string $id
     * @Rest\Get("/modflowmodels/{id}/timeunit")
     * @return JsonResponse
     */
    public function getTimeUnitAction(string $id): JsonResponse
    {
        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);
        $timeUnit = $this->get('inowas.modflowmodel.model_finder')->getTimeUnitByModelId($modelId);

        if (! $timeUnit instanceof TimeUnit) {
            throw NotFoundException::withMessage(sprintf(
                'ModflowModel with id: \'%s\' not found.', $modelId->toString()
            ));
        }

        return new JsonResponse(['time_unit' => $timeUnit->toInt()]);
    }

    /**
     * Update soilmodelId of modflow model by id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update soilmodelId of modflow model by id.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param Request $request
     * @param string $id
     * @Rest\Put("/modflowmodels/{id}/timeunit")
     * @return Response
     */
    public function putTimeUnitAction(Request $request, string $id)
    {
        $userId = $this->getUserId();

        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);

        $content = $this->getContentAsArray($request);
        $this->assertContainsKey('time_unit', $content);

        $timeUnit = TimeUnit::fromInt((int)$content['time_unit']);
        $this->get('prooph_service_bus.modflow_command_bus')->dispatch(UpdateTimeUnit::byUserAndModel($userId, $modelId, $timeUnit));

        $response = new RedirectResponse(
            $this->generateUrl('get_time_unit', array('id' => $modelId->toString())),
            302
        );

        return $response;
    }
}
