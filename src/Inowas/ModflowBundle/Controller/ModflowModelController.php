<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Inowas\AppBundle\Model\User;
use Inowas\Common\Boundaries\Area;
use Inowas\Common\Boundaries\BoundaryName;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Geometry\Polygon;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\Modelname;
use Inowas\Common\Modflow\ModflowModelDescription;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Common\Soilmodel\SoilmodelId;
use Inowas\ModflowModel\Model\Command\ChangeModflowModelBoundingBox;
use Inowas\ModflowModel\Model\Command\ChangeModflowModelDescription;
use Inowas\ModflowModel\Model\Command\ChangeModflowModelGridSize;
use Inowas\ModflowModel\Model\Command\ChangeModflowModelName;
use Inowas\ModflowModel\Model\Command\ChangeModflowModelSoilmodelId;
use Inowas\ModflowModel\Model\Command\CreateModflowModel;
use Inowas\ModflowModel\Model\Command\UpdateAreaGeometry;
use Inowas\ModflowModel\Model\Command\UpdateLengthUnit;
use Inowas\ModflowModel\Model\Command\UpdateTimeUnit;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ramsey\Uuid\Uuid;
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
        $user = $this->getUser();

        if (! $user instanceof User) {
            return new JsonResponse([]);
        }

        $userId = UserId::fromString($this->getUser()->getId()->toString());

        return new JsonResponse(
            $this->get('inowas.modflowmodel.model_finder')->findModelsByBaseUserId($userId)
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
        $userId = UserId::fromString($this->getUser()->getId()->toString());

        $modelId = ModflowId::generate();
        $name = Modelname::fromString($content['name']);
        $description = ModflowModelDescription::fromString($content['description']);

        $areaGeometry = new Polygon($content['geometry']['coordinates'], 4326);
        $area = Area::create(BoundaryId::generate(), BoundaryName::fromString($name->toString().' Area'), $areaGeometry);

        $gridSize = GridSize::fromXY((int)$content['grid_size']['n_x'], (int)$content['grid_size']['n_y']);
        $timeUnit = TimeUnit::fromInt((int)$content['time_unit']);
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
        if (! Uuid::isValid($id)) {
            return InowasJsonInvalidUuidResponse::withId($id);
        }

        $modelId = ModflowId::fromString($id);
        return new JsonResponse(
            $this->get('inowas.modflowmodel.model_finder')->getModelDetailsByModelId($modelId)
        );
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
        if (! Uuid::isValid($id)) {
            return InowasJsonInvalidUuidResponse::withId($id);
        }

        $modelId = ModflowId::fromString($id);
        $name = $this->get('inowas.modflowmodel.model_finder')->getModelNameByModelId($modelId);

        if (! $name instanceof Modelname) {
            return new InowasJsonNotFoundResponse(sprintf('Model with id %s not found.', $modelId->toString()));
        }

        return new JsonResponse([
            'name' => $name->toString()
        ]);
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
        if (! Uuid::isValid($id)) {
            return InowasJsonInvalidUuidResponse::withId($id);
        }

        $modelId = ModflowId::fromString($id);


        $content = $this->getContentAsArray($request);
        if (! array_key_exists('name', $content)){
            return InowasJsonInvalidInputResponse::withMessage('Expected key \'name\' not found.');
        }

        /** @var User $user */
        $user = $this->getUser();
        $userId = UserId::fromString($user->getId()->toString());

        if (! $this->get('inowas.modflowmodel.model_finder')->userHasWriteAccessToModel($userId, $modelId)){
            return InowasJsonWriteAccessDeniedResponse::withMessage(sprintf('User with Id %s does not have write access to ModflowModel %s.', $userId->toString(), $modelId->toString()));
        }

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
        if (! Uuid::isValid($id)) {
            return InowasJsonInvalidUuidResponse::withId($id);
        }

        $modelId = ModflowId::fromString($id);
        return new JsonResponse([
            'description' => $this->get('inowas.modflowmodel.model_finder')->getModelDescriptionByModelId($modelId)->toString()
        ]);
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
        if (! Uuid::isValid($id)) {
            return InowasJsonInvalidUuidResponse::withId($id);
        }

        $modelId = ModflowId::fromString($id);


        $content = $this->getContentAsArray($request);
        if (! array_key_exists('description', $content)){
            return InowasJsonInvalidInputResponse::withMessage('Expected key \'description\' not found.');
        }
        $description = ModflowModelDescription::fromString($content['description']);

        /** @var User $user */
        $user = $this->getUser();
        $userId = UserId::fromString($user->getId()->toString());

        if (! $this->get('inowas.modflowmodel.model_finder')->userHasWriteAccessToModel($userId, $modelId)){
            return InowasJsonWriteAccessDeniedResponse::withMessage(sprintf('User with Id %s does not have write access to ModflowModel %s.', $userId->toString(), $modelId->toString()));
        }

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
        if (! Uuid::isValid($id)) {
            return InowasJsonInvalidUuidResponse::withId($id);
        }

        $modelId = ModflowId::fromString($id);
        $geometry = $this->get('inowas.modflowmodel.model_finder')->getAreaGeometryByModflowModelId($modelId);

        if (! $geometry instanceof Geometry) {
            return new JsonResponse([]);
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
        if (! Uuid::isValid($id)) {
            return InowasJsonInvalidUuidResponse::withId($id);
        }

        $modelId = ModflowId::fromString($id);


        $content = $this->getContentAsArray($request);
        if (! array_key_exists('geometry', $content)){
            return InowasJsonInvalidInputResponse::withMessage('Expected key \'geometry\' not found.');
        }

        $polygon = new Polygon($content['geometry']['coordinates']);


        /** @var User $user */
        $user = $this->getUser();
        $userId = UserId::fromString($user->getId()->toString());

        if (! $this->get('inowas.modflowmodel.model_finder')->userHasWriteAccessToModel($userId, $modelId)){
            return InowasJsonWriteAccessDeniedResponse::withMessage(sprintf('User with Id %s does not have write access to ModflowModel %s.', $userId->toString(), $modelId->toString()));
        }

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
        if (! Uuid::isValid($id)) {
            return InowasJsonInvalidUuidResponse::withId($id);
        }

        $modelId = ModflowId::fromString($id);
        return new JsonResponse(['bounding_box' => $this->get('inowas.modflowmodel.model_finder')->getBoundingBoxByModflowModelId($modelId)]);
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
        if (! Uuid::isValid($id)) {
            return InowasJsonInvalidUuidResponse::withId($id);
        }

        $modelId = ModflowId::fromString($id);

        $content = $this->getContentAsArray($request);
        if (! array_key_exists('bounding_box', $content)){
            return InowasJsonInvalidInputResponse::withMessage('Expected key \'bounding_box\' not found.');
        }

        $boundingBox = BoundingBox::fromCoordinates(
            $content['bounding_box']['x_min'],
            $content['bounding_box']['x_max'],
            $content['bounding_box']['y_min'],
            $content['bounding_box']['y_max'],
            4326,
            0,
            0
        );

        /** @var User $user */
        $user = $this->getUser();
        $userId = UserId::fromString($user->getId()->toString());

        if (! $this->get('inowas.modflowmodel.model_finder')->userHasWriteAccessToModel($userId, $modelId)){
            return InowasJsonWriteAccessDeniedResponse::withMessage(sprintf('User with Id %s does not have write access to ModflowModel %s.', $userId->toString(), $modelId->toString()));
        }

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
        if (! Uuid::isValid($id)) {
            return InowasJsonInvalidUuidResponse::withId($id);
        }

        $modelId = ModflowId::fromString($id);
        $gridSize = $this->get('inowas.modflowmodel.model_finder')->getGridSizeByModflowModelId($modelId);

        if (! $gridSize instanceof GridSize) {
            return new InowasJsonNotFoundResponse(sprintf('Model with id %s not found.', $modelId->toString()));
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
        if (! Uuid::isValid($id)) {
            return InowasJsonInvalidUuidResponse::withId($id);
        }

        $modelId = ModflowId::fromString($id);

        $content = $this->getContentAsArray($request);
        if (! array_key_exists('grid_size', $content)) {
            return InowasJsonInvalidInputResponse::withMessage('Expected key \'grid_size\' not found.');
        }

        $gridSize = GridSize::fromXY($content['grid_size']['n_x'], $content['grid_size']['n_y']);

        /** @var User $user */
        $user = $this->getUser();
        $userId = UserId::fromString($user->getId()->toString());

        if (! $this->get('inowas.modflowmodel.model_finder')->userHasWriteAccessToModel($userId, $modelId)){
            return InowasJsonWriteAccessDeniedResponse::withMessage(sprintf('User with Id %s does not have write access to ModflowModel %s.', $userId->toString(), $modelId->toString()));
        }

        $this->get('prooph_service_bus.modflow_command_bus')->dispatch(ChangeModflowModelGridSize::forModflowModel($userId, $modelId, $gridSize));

        $response = new RedirectResponse(
            $this->generateUrl('get_modflow_model_grid_size', array('id' => $modelId->toString())),
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
        if (! Uuid::isValid($id)) {
            return InowasJsonInvalidUuidResponse::withId($id);
        }

        $modelId = ModflowId::fromString($id);
        $soilModelId = $this->get('inowas.modflowmodel.model_finder')->getSoilmodelIdByModelId($modelId);

        if (! $soilModelId instanceof SoilmodelId) {
            return new InowasJsonNotFoundResponse(sprintf('Model with id %s not found.', $modelId->toString()));
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
        if (! Uuid::isValid($id)) {
            return InowasJsonInvalidUuidResponse::withId($id);
        }

        $modelId = ModflowId::fromString($id);

        $content = $this->getContentAsArray($request);
        if (! array_key_exists('soilmodel_id', $content)) {
            return InowasJsonInvalidInputResponse::withMessage('Expected key \'soilmodel_id\' not found.');
        }

        $soilmodelId = SoilmodelId::fromString($content['soilmodel_id']);

        /** @var User $user */
        $user = $this->getUser();
        $userId = UserId::fromString($user->getId()->toString());

        if (! $this->get('inowas.modflowmodel.model_finder')->userHasWriteAccessToModel($userId, $modelId)){
            return InowasJsonWriteAccessDeniedResponse::withMessage(sprintf('User with Id %s does not have write access to ModflowModel %s.', $userId->toString(), $modelId->toString()));
        }

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
        if (! Uuid::isValid($id)) {
            return InowasJsonInvalidUuidResponse::withId($id);
        }

        $modelId = ModflowId::fromString($id);
        $lengthUnit = $this->get('inowas.modflowmodel.model_finder')->getLengthUnitByModelId($modelId);

        if (! $lengthUnit instanceof LengthUnit) {
            return new InowasJsonNotFoundResponse(sprintf('Model with id %s not found.', $modelId->toString()));
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
        if (! Uuid::isValid($id)) {
            return InowasJsonInvalidUuidResponse::withId($id);
        }

        $modelId = ModflowId::fromString($id);

        $content = $this->getContentAsArray($request);
        if (! array_key_exists('length_unit', $content)) {
            return InowasJsonInvalidInputResponse::withMessage('Expected key \'length_unit\' not found.');
        }

        $lengthUnit = LengthUnit::fromInt((int)$content['length_unit']);

        /** @var User $user */
        $user = $this->getUser();
        $userId = UserId::fromString($user->getId()->toString());

        if (! $this->get('inowas.modflowmodel.model_finder')->userHasWriteAccessToModel($userId, $modelId)){
            return InowasJsonWriteAccessDeniedResponse::withMessage(sprintf('User with Id %s does not have write access to ModflowModel %s.', $userId->toString(), $modelId->toString()));
        }

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
        if (! Uuid::isValid($id)) {
            return InowasJsonInvalidUuidResponse::withId($id);
        }

        $modelId = ModflowId::fromString($id);
        $timeUnit = $this->get('inowas.modflowmodel.model_finder')->getTimeUnitByModelId($modelId);

        if (! $timeUnit instanceof TimeUnit) {
            return new InowasJsonNotFoundResponse(sprintf('Model with id %s not found.', $modelId->toString()));
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
        if (! Uuid::isValid($id)) {
            return InowasJsonInvalidUuidResponse::withId($id);
        }

        $modelId = ModflowId::fromString($id);

        $content = $this->getContentAsArray($request);
        if (! array_key_exists('time_unit', $content)) {
            return InowasJsonInvalidInputResponse::withMessage('Expected key \'time_unit\' not found.');
        }

        $timeUnit = TimeUnit::fromInt((int)$content['time_unit']);

        /** @var User $user */
        $user = $this->getUser();
        $userId = UserId::fromString($user->getId()->toString());

        if (! $this->get('inowas.modflowmodel.model_finder')->userHasWriteAccessToModel($userId, $modelId)){
            return InowasJsonWriteAccessDeniedResponse::withMessage(sprintf('User with Id %s does not have write access to ModflowModel %s.', $userId->toString(), $modelId->toString()));
        }

        $this->get('prooph_service_bus.modflow_command_bus')->dispatch(UpdateTimeUnit::byUserAndModel($userId, $modelId, $timeUnit));

        $response = new RedirectResponse(
            $this->generateUrl('get_time_unit', array('id' => $modelId->toString())),
            302
        );

        return $response;
    }
}
