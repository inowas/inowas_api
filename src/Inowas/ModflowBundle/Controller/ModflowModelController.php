<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Inowas\Common\Geometry\Polygon;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\ModelName;
use Inowas\Common\Modflow\ModelDescription;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Common\Soilmodel\SoilmodelId;
use Inowas\ModflowBundle\Exception\NotFoundException;
use Inowas\ModflowModel\Model\Command\ModflowModel\CalculateModflowModel;
use Inowas\ModflowModel\Model\Command\ModflowModel\ChangeBoundingBox;
use Inowas\ModflowModel\Model\Command\ModflowModel\ChangeDescription;
use Inowas\ModflowModel\Model\Command\ModflowModel\ChangeGridSize;
use Inowas\ModflowModel\Model\Command\ModflowModel\ChangeName;
use Inowas\ModflowModel\Model\Command\ModflowModel\ChangeSoilmodelId;
use Inowas\ModflowModel\Model\Command\ModflowModel\CreateModflowModel;
use Inowas\ModflowModel\Model\Command\ModflowModel\DeleteModflowModel;
use Inowas\ModflowModel\Model\Command\ModflowModel\UpdateAreaGeometry;
use Inowas\ModflowModel\Model\Command\ModflowModel\UpdateAreaActiveCells;
use Inowas\ModflowModel\Model\Command\ModflowModel\UpdateLengthUnit;
use Inowas\ModflowModel\Model\Command\ModflowModel\UpdateTimeUnit;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/** @noinspection LongInheritanceChainInspection */
class ModflowModelController extends InowasRestController
{
    # MODEL-LISTING
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

    # MODEL-CREATION AND MODIFICATION
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
     * @throws \InvalidArgumentException
     * @throws \Prooph\ServiceBus\Exception\CommandDispatchException
     */
    public function postModflowModelsAction(Request $request): RedirectResponse
    {
        $content = $this->getContentAsArray($request);

        $userId = $this->getUserId();
        $modelId = ModflowId::generate();

        $this->assertContainsKey('name', $content);
        $name = ModelName::fromString($content['name']);

        $this->assertContainsKey('description', $content);
        $description = ModelDescription::fromString($content['description']);

        $this->assertContainsKey('area_geometry', $content);
        $polygon = new Polygon($content['area_geometry']['coordinates'], 4326);

        $this->assertContainsKey('grid_size', $content);
        $gridSize = GridSize::fromXY((int)$content['grid_size']['n_x'], (int)$content['grid_size']['n_y']);

        $this->assertContainsKey('time_unit', $content);
        $timeUnit = TimeUnit::fromInt((int)$content['time_unit']);

        $this->assertContainsKey('length_unit', $content);
        $lengthUnit = LengthUnit::fromInt((int)$content['length_unit']);

        $commandBus = $this->get('prooph_service_bus.modflow_command_bus');
        $commandBus->dispatch(CreateModflowModel::newWithAllParams(
            $userId, $modelId, $name, $description, $polygon, $gridSize, $timeUnit, $lengthUnit, SoilmodelId::generate()
        ));

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
     * @throws \Inowas\ModflowBundle\Exception\NotFoundException
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

        $activeCells = $this->get('inowas.modflowmodel.boundary_manager')->getAreaActiveCells($modelId);
        $details['active_cells'] = $activeCells->to2DArray();

        return new JsonResponse($details);
    }

    /**
     * Update details of modflow model by id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update details of modflow model by id.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param Request $request
     * @param string $id
     * @return RedirectResponse
     * @throws \InvalidArgumentException
     * @throws \Prooph\ServiceBus\Exception\CommandDispatchException
     * @Rest\Put("/modflowmodels/{id}")
     */
    public function putModflowModelAction(Request $request, string $id): RedirectResponse
    {
        $userId = $this->getUserId();
        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);

        $content = $this->getContentAsArray($request);

        if ($this->containsKey('name', $content)) {
            $name = ModelName::fromString($content['name']);
            $this->get('prooph_service_bus.modflow_command_bus')->dispatch(ChangeName::forModflowModel($userId, $modelId, $name));
        }

        if ($this->containsKey('description', $content)) {
            $description = ModelDescription::fromString($content['description']);
            $this->get('prooph_service_bus.modflow_command_bus')->dispatch(ChangeDescription::forModflowModel($userId, $modelId, $description));
        }

        if ($this->containsKey('geometry', $content)){
            $this->assertGeometryIsValid($content['geometry']);
            $polygon = new Polygon($content['geometry']['coordinates']);
            $this->get('prooph_service_bus.modflow_command_bus')->dispatch(UpdateAreaGeometry::of($userId, $modelId, $polygon));
        }

        if ($this->containsKey('bounding_box', $content)){
            $boundingBox = BoundingBox::fromCoordinates(
                $content['bounding_box']['x_min'],
                $content['bounding_box']['x_max'],
                $content['bounding_box']['y_min'],
                $content['bounding_box']['y_max'],
                4326
            );

            $this->get('prooph_service_bus.modflow_command_bus')->dispatch(ChangeBoundingBox::forModflowModel($userId, $modelId, $boundingBox));
        }

        if ($this->containsKey('grid_size', $content)) {
            $gridSize = GridSize::fromXY($content['grid_size']['n_x'], $content['grid_size']['n_y']);
            $this->get('prooph_service_bus.modflow_command_bus')->dispatch(ChangeGridSize::forModflowModel($userId, $modelId, $gridSize));
        }

        if ($this->containsKey('length_unit', $content)) {
            $lengthUnit = LengthUnit::fromInt((int)$content['length_unit']);
            $this->get('prooph_service_bus.modflow_command_bus')->dispatch(UpdateLengthUnit::byUserAndModel($userId, $modelId, $lengthUnit));
        }

        if ($this->containsKey('time_unit', $content)) {
            $timeUnit = TimeUnit::fromInt((int)$content['time_unit']);
            $this->get('prooph_service_bus.modflow_command_bus')->dispatch(UpdateTimeUnit::byUserAndModel($userId, $modelId, $timeUnit));
        }

        if ($this->containsKey('soilmodel_id', $content)) {
            $soilmodelId = SoilmodelId::fromString($content['soilmodel_id']);
            $this->get('prooph_service_bus.modflow_command_bus')->dispatch(ChangeSoilmodelId::forModflowModel($userId, $modelId, $soilmodelId));
        }

        if ($this->containsKey('active_cells', $content)) {
            $activeCells = ActiveCells::from2DArray($content['active_cells']);
            $this->get('prooph_service_bus.modflow_command_bus')->dispatch(UpdateAreaActiveCells::ofModelArea($userId, $modelId, $activeCells));
        }

        return new RedirectResponse(
            $this->generateUrl('get_modflow_model', array('id' => $modelId->toString())),
            303
        );
    }

    /**
     * Delete a modflow-model.
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
     * @Rest\Delete("/modflowmodels/{id}")
     * @return Response
     * @throws \InvalidArgumentException
     * @throws \Prooph\ServiceBus\Exception\CommandDispatchException
     * @throws \Inowas\ModflowBundle\Exception\NotFoundException
     */
    public function deleteModflowModelAction(string $id): Response
    {
        $userId = $this->getUserId();
        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);

        $this->get('prooph_service_bus.modflow_command_bus')->dispatch(DeleteModflowModel::byIdAndUser($modelId, $userId));

        return new Response('', 201);
    }

    /* Calculation */
    /**
     * Calculate the model.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Calculate the model.",
     *   statusCodes = {
     *     302 = "Redirect to /modflowmodels/{id}/calculation"
     *   }
     * )
     *
     * @Rest\Post("/modflowmodels/{id}/calculate")
     * @param string $id
     * @return RedirectResponse
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Prooph\ServiceBus\Exception\CommandDispatchException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    public function postModflowModelCalculateAction(string $id): RedirectResponse
    {
        $userId = $this->getUserId();

        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);

        $this->container->get('prooph_service_bus.modflow_command_bus')->dispatch(
            CalculateModflowModel::forModflowModel($userId, $modelId)
        );

        $response = new RedirectResponse(
            $this->generateUrl('get_modflow_model_calculation', array('id' => $modelId->toString())),
            302
        );

        return $response;
    }

    /**
     * Get details of last calculation of modflow model by id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get details of last calculation of modflow model by id.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param string $id
     * @Rest\Get("/modflowmodels/{id}/calculation")
     * @return RedirectResponse
     * @throws \InvalidArgumentException
     * @throws NotFoundException
     */
    public function getModflowModelCalculationAction(string $id): RedirectResponse
    {
        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);
        $calculationId = $this->get('inowas.modflowmodel.model_finder')->getCalculationIdByModelId($modelId);

        return new RedirectResponse(
            $this->generateUrl('get_calculation_details', array('id' => $calculationId->toString())),
            302
        );
    }

    /* Soilmodel */
    /**
     * Get soilmodel details of modflowModel by id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get soilmodel details of modflowModel by id.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param string $id
     * @Rest\Get("/modflowmodels/{id}/soilmodel")
     * @return RedirectResponse
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Prooph\ServiceBus\Exception\CommandDispatchException
     * @throws \InvalidArgumentException
     */
    public function getSoilModelAction(string $id): RedirectResponse
    {
        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);

        $soilmodelId = $this->container->get('inowas.modflowmodel.model_finder')->getSoilmodelIdByModelId($modelId);

        return new RedirectResponse(
            $this->generateUrl('get_soilmodel', array('id' => $soilmodelId->toString())),
            302
        );
    }
}
