<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Inowas\AppBundle\Model\User;
use Inowas\Common\Boundaries\BoundaryName;
use Inowas\Common\Boundaries\BoundaryType;
use Inowas\Common\Boundaries\ConstantHeadBoundary;
use Inowas\Common\Boundaries\GeneralHeadBoundary;
use Inowas\Common\Boundaries\ObservationPoint;
use Inowas\Common\Boundaries\ObservationPointName;
use Inowas\Common\Boundaries\RechargeBoundary;
use Inowas\Common\Boundaries\RiverBoundary;
use Inowas\Common\Boundaries\WellBoundary;
use Inowas\Common\Boundaries\WellType;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Grid\AffectedLayers;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\ObservationPointId;
use Inowas\Common\Id\UserId;
use Inowas\ModflowModel\Model\Command\AddBoundary;
use Inowas\ModflowModel\Model\Command\AddObservationPointToBoundary;
use Inowas\ModflowModel\Model\Command\UpdateBoundaryGeometry;
use Inowas\ModflowModel\Model\Command\UpdateBoundaryName;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ModflowModelBoundaryController extends InowasRestController
{
    /**
     * Get list of boundaries from modflowmodel by id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns a list of all boundaries with ids, types, names, geometry.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param string $id
     * @Rest\Get("/modflowmodels/{id}/boundaries")
     * @return JsonResponse
     */
    public function getModflowModelBoundariesAction(string $id): JsonResponse
    {
        if (! Uuid::isValid($id)) {
            return InowasJsonInvalidUuidResponse::withId($id);
        }

        $modelId = ModflowId::fromString($id);
        $boundaries = $this->get('inowas.modflowmodel.boundaries_finder')->findByModelId($modelId);

        $response = array();
        foreach ($boundaries as $boundary){
            $response[] = [
                'id' => $boundary['id'],
                'name' => $boundary['name'],
                'type' => $boundary['type'],
                'geometry' => json_decode($boundary['geometry']),
                'affected_layers' => json_decode($boundary['affected_layers']),
                'metadata' => json_decode($boundary['metadata'])
            ];
        }

        return new JsonResponse($response);
    }

    /**
     * Create a new Boundary with name, type, geometry.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a new Boundary with name, type, geometry.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param string $id
     * @param Request $request
     * @Rest\Post("/modflowmodels/{id}/boundaries")
     * @return Response
     */
    public function postModflowModelBoundariesAction(string $id, Request $request): Response
    {
        if (! Uuid::isValid($id)) {
            return InowasJsonInvalidUuidResponse::withId($id);
        }

        $modelId = ModflowId::fromString($id);
        $userId = UserId::fromString($this->getUser()->getId()->toString());

        $content = $this->getContentAsArray($request);
        if (! array_key_exists('name', $content)) {
            return InowasJsonInvalidInputResponse::withMessage('Expected key \'name\' not found.');
        }

        $name = BoundaryName::fromString($content['name']);

        if (! array_key_exists('type', $content)) {
            return InowasJsonInvalidInputResponse::withMessage('Expected key \'type\' not found.');
        }

        $type = BoundaryType::fromString($content['type']);

        if (! array_key_exists('geometry', $content)) {
            return InowasJsonInvalidInputResponse::withMessage('Expected key \'geometry\' not found.');
        }

        $geometry = Geometry::fromArray($content['geometry']);

        $boundaryId = BoundaryId::generate();
        $boundary = null;
        switch ($type->toString()) {
            case BoundaryType::CONSTANT_HEAD:

                if (! array_key_exists('affected_layers', $content)) {
                    return InowasJsonInvalidInputResponse::withMessage('Expected key \'affected_layers\' not found.');
                }

                $affectedLayers = AffectedLayers::fromArray($content['affected_layers']);

                $boundary = ConstantHeadBoundary::createWithParams(
                    $boundaryId,
                    $name,
                    $geometry,
                    $affectedLayers
                );
                break;

            case BoundaryType::GENERAL_HEAD:

                if (! array_key_exists('affected_layers', $content)) {
                    return InowasJsonInvalidInputResponse::withMessage('Expected key \'affected_layers\' not found.');
                }

                $affectedLayers = AffectedLayers::fromArray($content['affected_layers']);

                $boundary = GeneralHeadBoundary::createWithParams(
                    $boundaryId,
                    $name,
                    $geometry,
                    $affectedLayers
                );
                break;

            case BoundaryType::RECHARGE:
                $boundary = RechargeBoundary::createWithParams(
                    $boundaryId,
                    $name,
                    $geometry
                );
                break;

            case BoundaryType::RIVER:
                $boundary = RiverBoundary::createWithParams(
                    $boundaryId,
                    $name,
                    $geometry
                );
                break;

            case BoundaryType::WELL:

                if (! array_key_exists('affected_layers', $content)) {
                    return InowasJsonInvalidInputResponse::withMessage('Expected key \'affected_layers\' not found.');
                }

                $affectedLayers = AffectedLayers::fromArray($content['affected_layers']);

                if (! array_key_exists('metadata', $content)) {
                    return InowasJsonInvalidInputResponse::withMessage('Expected key \'metadata\' not found.');
                }

                $metadata = $content['metadata'];

                if (! array_key_exists('well_type', $metadata)) {
                    return InowasJsonInvalidInputResponse::withMessage('Expected key \'well_type\' not found in metadata.');
                }

                $wellType = WellType::fromString($metadata['well_type']);


                $boundary = WellBoundary::createWithParams(
                    $boundaryId,
                    $name,
                    $geometry,
                    $wellType,
                    $affectedLayers
                );

                break;
        }

        if (null === $boundary){
            return InowasJsonInvalidInputResponse::withMessage(
                sprintf('BoundaryType %s not known', $type->toString())
            );

        }

        $commandBus = $this->get('prooph_service_bus.modflow_command_bus');
        $commandBus->dispatch(AddBoundary::to($modelId, $userId, $boundary));

        return new RedirectResponse(
            $this->generateUrl('get_modflow_model_boundary', array('id' => $modelId->toString(), 'bid' => $boundaryId->toString())),
            302
        );
    }

    /**
     * Get boundary details details by modflow model id and boundary id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns all boundary details with observationpoints and values.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param string $id
     * @param string $bid
     * @Rest\Get("/modflowmodels/{id}/boundaries/{bid}")
     * @return Response
     */
    public function getModflowModelBoundaryAction(string $id, string $bid): Response
    {
        if (! Uuid::isValid($id)) {
            return InowasJsonInvalidUuidResponse::withId($id);
        }

        $modelId = ModflowId::fromString($id);

        if (! Uuid::isValid($bid)) {
            return InowasJsonInvalidUuidResponse::withId($bid);
        }

        $boundaryId = BoundaryId::fromString($bid);

        $boundaryDetails = $this->get('inowas.modflowmodel.boundaries_finder')->getBoundaryDetails($modelId, $boundaryId);

        if (null === $boundaryDetails){
            return InowasJsonNotFoundResponse::withMessage(sprintf('Boundary with id %s in Model with id %s not found.', $boundaryId, $modelId));
        }

        return new JsonResponse($boundaryDetails);
    }

    /**
     * Get boundaryName by modflow model id and boundary id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get boundaryName by modflow model id and boundary id.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param string $id
     * @param string $bid
     * @Rest\Get("/modflowmodels/{id}/boundaries/{bid}/name")
     * @return Response
     */
    public function getModflowModelBoundaryNameAction(string $id, string $bid): Response
    {
        $modelId = ModflowId::fromString($id);
        $boundaryId = BoundaryId::fromString($bid);
        $boundaryName = $this->get('inowas.modflowmodel.boundaries_finder')->getBoundaryName($modelId, $boundaryId);

        if (! $boundaryName instanceof BoundaryName){
            return InowasJsonNotFoundResponse::withMessage(sprintf('Boundary with id %s in Model with id %s not found.', $boundaryId, $modelId));
        }

        return new JsonResponse(['name' => $boundaryName]);
    }

    /**
     * Update boundaryName by modflow model id and boundary id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update boundaryName by modflow model id and boundary id.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param string $id
     * @param string $bid
     * @param Request $request
     * @Rest\Put("/modflowmodels/{id}/boundaries/{bid}/name")
     * @return Response
     */
    public function putModflowModelBoundaryNameAction(string $id, string $bid, Request $request): Response
    {
        if (! Uuid::isValid($id)) {
            return InowasJsonInvalidUuidResponse::withId($id);
        }

        $modelId = ModflowId::fromString($id);

        if (! Uuid::isValid($bid)) {
            return InowasJsonInvalidUuidResponse::withId($id);
        }

        $boundaryId = BoundaryId::fromString($bid);

        $content = $this->getContentAsArray($request);
        if (! array_key_exists('name', $content)){
            return InowasJsonInvalidInputResponse::withMessage('Expected key \'name\' not found.');
        }

        $boundaryName = BoundaryName::fromString($content['name']);

        /** @var User $user */
        $user = $this->getUser();
        $userId = UserId::fromString($user->getId()->toString());

        if (! $this->get('inowas.modflowmodel.model_finder')->userHasWriteAccessToModel($userId, $modelId)){
            return InowasJsonWriteAccessDeniedResponse::withMessage(sprintf('User with Id %s does not have write access to ModflowModel %s.', $userId->toString(), $modelId->toString()));
        }


        $this->get('prooph_service_bus.modflow_command_bus')->dispatch(UpdateBoundaryName::byUserModelAndBoundary($userId, $modelId, $boundaryId, $boundaryName));

        return new RedirectResponse(
            $this->generateUrl('get_modflow_model_boundary_name', array('id' => $modelId->toString(), 'bid' => $boundaryId->toString())),
            302
        );
    }

    /**
     * Get boundary geometry by modflow model id and boundary id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get boundary geometry by modflow model id and boundary id.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param string $id
     * @param string $bid
     * @Rest\Get("/modflowmodels/{id}/boundaries/{bid}/geometry")
     * @return Response
     */
    public function getModflowModelBoundaryGeometryAction(string $id, string $bid): Response
    {
        $modelId = ModflowId::fromString($id);
        $boundaryId = BoundaryId::fromString($bid);
        $geometry = $this->get('inowas.modflowmodel.boundaries_finder')->getBoundaryGeometry($modelId, $boundaryId);

        if (null === $geometry){
            return InowasJsonNotFoundResponse::withMessage(sprintf('Boundary with id %s in Model with id %s not found.', $boundaryId, $modelId));
        }

        return new JsonResponse(['geometry' => $geometry]);
    }

    /**
     * Update boundary geometry by modflow model id and boundary id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update boundary geometry by modflow model id and boundary id.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param string $id
     * @param string $bid
     * @param Request $request
     * @Rest\Put("/modflowmodels/{id}/boundaries/{bid}/geometry")
     * @return Response
     */
    public function putModflowModelBoundaryGeometryAction(string $id, string $bid, Request $request): Response
    {
        if (! Uuid::isValid($id)) {
            return InowasJsonInvalidUuidResponse::withId($id);
        }

        $modelId = ModflowId::fromString($id);

        if (! Uuid::isValid($bid)) {
            return InowasJsonInvalidUuidResponse::withId($id);
        }

        $boundaryId = BoundaryId::fromString($bid);

        $content = $this->getContentAsArray($request);
        if (! array_key_exists('geometry', $content)){
            return InowasJsonInvalidInputResponse::withMessage('Expected key \'geometry\' not found.');
        }

        $geometry = Geometry::fromArray($content['geometry']);

        /** @var User $user */
        $user = $this->getUser();
        $userId = UserId::fromString($user->getId()->toString());

        if (! $this->get('inowas.modflowmodel.model_finder')->userHasWriteAccessToModel($userId, $modelId)){
            return InowasJsonWriteAccessDeniedResponse::withMessage(sprintf('User with Id %s does not have write access to ModflowModel %s.', $userId->toString(), $modelId->toString()));
        }

        $this->get('prooph_service_bus.modflow_command_bus')->dispatch(UpdateBoundaryGeometry::byUser($userId, $modelId, $boundaryId, $geometry));

        return new RedirectResponse(
            $this->generateUrl('get_modflow_model_boundary_geometry', array('id' => $modelId->toString(), 'bid' => $boundaryId->toString())),
            302
        );
    }

    /**
     * Update boundary geometry by modflow model id and boundary id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update boundary geometry by modflow model id and boundary id.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param string $id
     * @param string $bid
     * @param Request $request
     * @Rest\Post("/modflowmodels/{id}/boundaries/{bid}/observationpoints")
     * @return Response
     */
    public function postModflowModelBoundaryObservationPointAction(string $id, string $bid, Request $request): Response
    {
        if (! Uuid::isValid($id)) {
            return InowasJsonInvalidUuidResponse::withId($id);
        }

        $modelId = ModflowId::fromString($id);

        if (! Uuid::isValid($bid)) {
            return InowasJsonInvalidUuidResponse::withId($id);
        }

        $boundaryId = BoundaryId::fromString($bid);

        $content = $this->getContentAsArray($request);

        if (! array_key_exists('name', $content)){
            return InowasJsonInvalidInputResponse::withMessage('Expected key \'name\' not found.');
        }

        $name = ObservationPointName::fromString($content['name']);

        if (! array_key_exists('geometry', $content)){
            return InowasJsonInvalidInputResponse::withMessage('Expected key \'geometry\' not found.');
        }

        $geometry = Geometry::fromArray($content['geometry']);


        /** @var User $user */
        $user = $this->getUser();
        $userId = UserId::fromString($user->getId()->toString());

        if (! $this->get('inowas.modflowmodel.model_finder')->userHasWriteAccessToModel($userId, $modelId)){
            return InowasJsonWriteAccessDeniedResponse::withMessage(sprintf('User with Id %s does not have write access to ModflowModel %s.', $userId->toString(), $modelId->toString()));
        }

        $observationPointId = ObservationPointId::generate();
        $observationPoint = ObservationPoint::fromIdNameAndGeometry($observationPointId, $name, $geometry);

        $this->get('prooph_service_bus.modflow_command_bus')->dispatch(AddObservationPointToBoundary::byUserModelIdBoundaryId($userId, $modelId, $boundaryId, $observationPoint));

        return new RedirectResponse(
            $this->generateUrl('get_modflow_model_boundary_geometry', array('id' => $modelId->toString(), 'bid' => $boundaryId->toString())),
            302
        );
    }

    /**
     * Get details of the observationpoint by modflowModelId, boundaryId and observationPointId.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get details of the observationpoint by modflowModelId, boundaryId and observationPointId",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param string $id
     * @param string $bid
     * @param string $oid
     * @Rest\Get("/modflowmodels/{id}/boundaries/{bid}/observationpoints/{oid}")
     * @return Response
     */
    public function getModflowModelBoundaryObservationPointDetailsAction(string $id, string $bid, string $oid): Response
    {
        if (! Uuid::isValid($id)) {
            return InowasJsonInvalidUuidResponse::withId($id);
        }

        $modelId = ModflowId::fromString($id);

        if (! Uuid::isValid($bid)) {
            return InowasJsonInvalidUuidResponse::withId($bid);
        }

        $boundaryId = BoundaryId::fromString($bid);

        if (! Uuid::isValid($oid)) {
            return InowasJsonInvalidUuidResponse::withId($oid);
        }

        $observationPointId = ObservationPointId::fromString($oid);
        $observationPointDetails = $this->get('inowas.modflowmodel.boundaries_finder')->getBoundaryObservationPointDetails($modelId, $boundaryId, $observationPointId);

        if (null === $observationPointDetails){
            return InowasJsonNotFoundResponse::withMessage(
                sprintf(
                    'ObservationPoint with Id %s Boundary with id %s in Model with id %s not found.',
                    $observationPointId->toString(),
                    $boundaryId->toString(),
                    $modelId->toString()
                )
            );
        }

        return new JsonResponse($observationPointDetails);
    }

    /**
     * Get name of the observationpoint by modflowModelId, boundaryId and observationPointId.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get name of the observationpoint by modflowModelId, boundaryId and observationPointId",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param string $id
     * @param string $bid
     * @param string $oid
     * @Rest\Get("/modflowmodels/{id}/boundaries/{bid}/observationpoints/{oid}/name")
     * @return Response
     */
    public function getModflowModelBoundaryObservationPointNameAction(string $id, string $bid, string $oid): Response
    {
        if (! Uuid::isValid($id)) {
            return InowasJsonInvalidUuidResponse::withId($id);
        }

        $modelId = ModflowId::fromString($id);

        if (! Uuid::isValid($bid)) {
            return InowasJsonInvalidUuidResponse::withId($bid);
        }

        $boundaryId = BoundaryId::fromString($bid);

        if (! Uuid::isValid($oid)) {
            return InowasJsonInvalidUuidResponse::withId($oid);
        }

        $observationPointId = ObservationPointId::fromString($oid);
        $name = $this->get('inowas.modflowmodel.boundaries_finder')->getBoundaryObservationPointName($modelId, $boundaryId, $observationPointId);

        if (! $name instanceof ObservationPointName){
            return InowasJsonNotFoundResponse::withMessage(
                sprintf(
                    'ObservationPoint with Id %s Boundary with id %s in Model with id %s not found.',
                    $observationPointId->toString(),
                    $boundaryId->toString(),
                    $modelId->toString()
                )
            );
        }

        return new JsonResponse(['name' => $name]);
    }

    /**
     * Get geometry of the observationpoint by modflowModelId, boundaryId and observationPointId.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get geometry of the observationpoint by modflowModelId, boundaryId and observationPointId",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param string $id
     * @param string $bid
     * @param string $oid
     * @Rest\Get("/modflowmodels/{id}/boundaries/{bid}/observationpoints/{oid}/geometry")
     * @return Response
     */
    public function getModflowModelBoundaryObservationPointGeometryAction(string $id, string $bid, string $oid): Response
    {
        if (! Uuid::isValid($id)) {
            return InowasJsonInvalidUuidResponse::withId($id);
        }

        $modelId = ModflowId::fromString($id);

        if (! Uuid::isValid($bid)) {
            return InowasJsonInvalidUuidResponse::withId($bid);
        }

        $boundaryId = BoundaryId::fromString($bid);

        if (! Uuid::isValid($oid)) {
            return InowasJsonInvalidUuidResponse::withId($oid);
        }

        $observationPointId = ObservationPointId::fromString($oid);
        $geometry = $this->get('inowas.modflowmodel.boundaries_finder')->getBoundaryObservationPointGeometry($modelId, $boundaryId, $observationPointId);

        if (! $geometry instanceof Geometry){
            return InowasJsonNotFoundResponse::withMessage(
                sprintf(
                    'ObservationPoint with Id %s Boundary with id %s in Model with id %s not found.',
                    $observationPointId->toString(),
                    $boundaryId->toString(),
                    $modelId->toString()
                )
            );
        }

        return new JsonResponse(['geometry' => $geometry->toArray()]);
    }

    /**
     * Get values of the observationpoint by modflowModelId, boundaryId and observationPointId.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get values of the observationpoint by modflowModelId, boundaryId and observationPointId",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param string $id
     * @param string $bid
     * @param string $oid
     * @Rest\Get("/modflowmodels/{id}/boundaries/{bid}/observationpoints/{oid}/values")
     * @return Response
     */
    public function getModflowModelBoundaryObservationPointValuesAction(string $id, string $bid, string $oid): Response
    {
        if (! Uuid::isValid($id)) {
            return InowasJsonInvalidUuidResponse::withId($id);
        }

        $modelId = ModflowId::fromString($id);

        if (! Uuid::isValid($bid)) {
            return InowasJsonInvalidUuidResponse::withId($bid);
        }

        $boundaryId = BoundaryId::fromString($bid);

        if (! Uuid::isValid($oid)) {
            return InowasJsonInvalidUuidResponse::withId($oid);
        }

        $observationPointId = ObservationPointId::fromString($oid);
        $values = $this->get('inowas.modflowmodel.boundaries_finder')->getBoundaryObservationPointValues($modelId, $boundaryId, $observationPointId);

        if (null === $values){
            return InowasJsonNotFoundResponse::withMessage(
                sprintf(
                    'ObservationPoint with Id %s Boundary with id %s in Model with id %s not found.',
                    $observationPointId->toString(),
                    $boundaryId->toString(),
                    $modelId->toString()
                )
            );
        }

        return new JsonResponse($values);
    }
}
