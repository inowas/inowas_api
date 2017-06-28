<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Inowas\Common\Boundaries\BoundaryName;
use Inowas\Common\Boundaries\BoundaryType;
use Inowas\Common\Boundaries\ConstantHeadBoundary;
use Inowas\Common\Boundaries\GeneralHeadBoundary;
use Inowas\Common\Boundaries\ObservationPointName;
use Inowas\Common\Boundaries\RechargeBoundary;
use Inowas\Common\Boundaries\RiverBoundary;
use Inowas\Common\Boundaries\WellBoundary;
use Inowas\Common\Boundaries\WellType;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Geometry\Point;
use Inowas\Common\Grid\AffectedLayers;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\ObservationPointId;
use Inowas\ModflowBundle\Exception\InvalidArgumentException;
use Inowas\ModflowBundle\Exception\NotFoundException;
use Inowas\ModflowModel\Model\Command\AddBoundary;
use Inowas\ModflowModel\Model\Command\CreateObservationPoint;
use Inowas\ModflowModel\Model\Command\UpdateBoundaryGeometry;
use Inowas\ModflowModel\Model\Command\UpdateBoundaryName;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/** @noinspection LongInheritanceChainInspection */
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
     * @throws \Inowas\ModflowBundle\Exception\NotFoundException
     */
    public function getModflowModelBoundariesAction(string $id): JsonResponse
    {
        $this->assertUserIsLoggedInCorrectly();

        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);

        $boundaries = $this->get('inowas.modflowmodel.boundary_finder')->findBoundariesByModelId($modelId);
        if (null === $boundaries) {
            throw NotFoundException::withMessage(sprintf(
                'ModflowModel with id: \'%s\' not found.', $modelId->toString()
            ));
        }

        $response = [];
        foreach ($boundaries as $boundary) {
            $boundary['geometry'] = json_decode($boundary['geometry']);
            $boundary['affected_layers'] = json_decode($boundary['affected_layers']);
            $boundary['metadata'] = json_decode($boundary['metadata']);

            $response[] = $boundary;
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
     * @throws \Inowas\ModflowBundle\Exception\InvalidArgumentException
     * @throws \Prooph\ServiceBus\Exception\CommandDispatchException
     * @throws \InvalidArgumentException
     */
    public function postModflowModelBoundariesAction(string $id, Request $request): Response
    {
        $userId = $this->getUserId();

        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);

        $content = $this->getContentAsArray($request);
        $this->assertContainsKey('name', $content);
        $name = BoundaryName::fromString($content['name']);

        $this->assertContainsKey('type', $content);
        $type = BoundaryType::fromString($content['type']);

        $this->assertContainsKey('geometry', $content);
        $this->assertGeometryIsValid($content['geometry']);
        $geometry = Geometry::fromArray($content['geometry']);

        $boundaryId = BoundaryId::generate();
        $boundary = null;
        switch ($type->toString()) {
            case BoundaryType::CONSTANT_HEAD:

                $this->assertContainsKey('affected_layers', $content);
                $affectedLayers = AffectedLayers::fromArray($content['affected_layers']);

                $boundary = ConstantHeadBoundary::createWithParams(
                    $boundaryId,
                    $name,
                    $geometry,
                    $affectedLayers
                );
                break;

            case BoundaryType::GENERAL_HEAD:

                $this->assertContainsKey('affected_layers', $content);
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

                $this->assertContainsKey('affected_layers', $content);
                $affectedLayers = AffectedLayers::fromArray($content['affected_layers']);
                $this->assertContainsKey('metadata', $content);
                $metadata = $content['metadata'];
                $this->assertContainsKey('well_type', $content);
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
            throw InvalidArgumentException::withMessage(
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
     * @throws \Inowas\ModflowBundle\Exception\NotFoundException
     */
    public function getModflowModelBoundaryAction(string $id, string $bid): Response
    {
        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);

        $this->assertUuidIsValid($bid);
        $boundaryId = BoundaryId::fromString($bid);

        $boundaryDetails = $this->get('inowas.modflowmodel.boundary_finder')->getBoundaryDetails($modelId, $boundaryId);

        if (null === $boundaryDetails){
            throw NotFoundException::withMessage(sprintf(
                'ModflowModel with id: \'%s\' and BoundaryId \'%s\' not found.', $modelId->toString(), $boundaryId->toString()
            ));
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
     * @throws \Inowas\ModflowBundle\Exception\NotFoundException
     */
    public function getModflowModelBoundaryNameAction(string $id, string $bid): Response
    {
        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);

        $this->assertUuidIsValid($bid);
        $boundaryId = BoundaryId::fromString($bid);

        $boundaryName = $this->get('inowas.modflowmodel.boundary_finder')->getBoundaryName($modelId, $boundaryId);

        if (! $boundaryName instanceof BoundaryName){
            throw NotFoundException::withMessage(sprintf(
                'ModflowModel with id: \'%s\' and BoundaryId \'%s\' not found.', $modelId->toString(), $boundaryId->toString()
            ));
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
     * @throws \Prooph\ServiceBus\Exception\CommandDispatchException
     * @throws \InvalidArgumentException
     */
    public function putModflowModelBoundaryNameAction(string $id, string $bid, Request $request): Response
    {
        $userId = $this->getUserId();

        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);

        $this->assertUuidIsValid($bid);
        $boundaryId = BoundaryId::fromString($bid);

        $content = $this->getContentAsArray($request);
        $this->assertContainsKey( 'name', $content);
        $boundaryName = BoundaryName::fromString($content['name']);

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
     * @throws \Inowas\ModflowBundle\Exception\NotFoundException
     */
    public function getModflowModelBoundaryGeometryAction(string $id, string $bid): Response
    {
        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);

        $this->assertUuidIsValid($bid);
        $boundaryId = BoundaryId::fromString($bid);

        $geometry = $this->get('inowas.modflowmodel.boundary_finder')->getBoundaryGeometry($modelId, $boundaryId);

        if (null === $geometry){
            throw NotFoundException::withMessage(sprintf(
                'ModflowModel with id: \'%s\' and BoundaryId \'%s\' not found.', $modelId->toString(), $boundaryId->toString()
            ));
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
     * @throws \Prooph\ServiceBus\Exception\CommandDispatchException
     * @throws \InvalidArgumentException
     */
    public function putModflowModelBoundaryGeometryAction(string $id, string $bid, Request $request): Response
    {
        $userId = $this->getUserId();

        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);

        $this->assertUuidIsValid($bid);
        $boundaryId = BoundaryId::fromString($bid);

        $content = $this->getContentAsArray($request);
        $this->assertContainsKey('geometry', $content);
        $this->assertGeometryIsValid($content['geometry']);
        $geometry = Geometry::fromArray($content['geometry']);

        $this->get('prooph_service_bus.modflow_command_bus')->dispatch(UpdateBoundaryGeometry::byUser($userId, $modelId, $boundaryId, $geometry));

        return new RedirectResponse(
            $this->generateUrl('get_modflow_model_boundary_geometry', array('id' => $modelId->toString(), 'bid' => $boundaryId->toString())),
            302
        );
    }

    /**
     * Create observation point on a boundary with modflowModelId and boundaryId.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create observation point on a boundary with modflowModelId and boundaryId.",
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
     * @throws \Inowas\ModflowBundle\Exception\InvalidArgumentException
     * @throws \Prooph\ServiceBus\Exception\CommandDispatchException
     * @throws \InvalidArgumentException
     */
    public function postModflowModelBoundaryObservationPointAction(string $id, string $bid, Request $request): Response
    {
        $userId = $this->getUserId();

        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);

        $this->assertUuidIsValid($bid);
        $boundaryId = BoundaryId::fromString($bid);

        $content = $this->getContentAsArray($request);
        $this->assertContainsKey('name', $content);
        $name = ObservationPointName::fromString($content['name']);

        $this->assertContainsKey('geometry', $content);
        $this->assertGeometryIsValid($content['geometry']);
        $geometry = Geometry::fromArray($content['geometry']);
        $point = $geometry->value();

        if (! $point instanceof Point) {
            throw InvalidArgumentException::withMessage(sprintf('Expected geometry is point. Found %s', $content['geometry']));
        }

        $observationPointId = ObservationPointId::generate();
        $this->get('prooph_service_bus.modflow_command_bus')->dispatch(
            CreateObservationPoint::byUserModelIdBoundaryId($userId, $modelId, $boundaryId, $observationPointId, $name, $geometry));

        return new RedirectResponse(
            $this->generateUrl('get_modflow_model_boundary_observation_point_details', array(
                'id' => $modelId->toString(),
                'bid' => $boundaryId->toString(),
                'oid' => $observationPointId->toString(),
                )
            ),
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
     * @throws \Inowas\ModflowBundle\Exception\NotFoundException
     */
    public function getModflowModelBoundaryObservationPointDetailsAction(string $id, string $bid, string $oid): Response
    {
        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);

        $this->assertUuidIsValid($bid);
        $boundaryId = BoundaryId::fromString($bid);

        $this->assertUuidIsValid($oid);
        $observationPointId = ObservationPointId::fromString($oid);

        $observationPointDetails = $this->get('inowas.modflowmodel.boundary_finder')->getBoundaryObservationPointDetails($modelId, $boundaryId, $observationPointId);

        if (null === $observationPointDetails){
            throw NotFoundException::withMessage(sprintf(
                'ModflowModel with id: \'%s\' and BoundaryId: \'%s\' and ObservationPointId: %s not found.',
                $modelId->toString(),
                $boundaryId->toString(),
                $observationPointId->toString()
            ));
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
     * @throws \Inowas\ModflowBundle\Exception\NotFoundException
     */
    public function getModflowModelBoundaryObservationPointNameAction(string $id, string $bid, string $oid): Response
    {
        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);

        $this->assertUuidIsValid($bid);
        $boundaryId = BoundaryId::fromString($bid);

        $this->assertUuidIsValid($oid);
        $observationPointId = ObservationPointId::fromString($oid);

        $name = $this->get('inowas.modflowmodel.boundary_finder')->getBoundaryObservationPointName($modelId, $boundaryId, $observationPointId);

        if (! $name instanceof ObservationPointName){
            throw NotFoundException::withMessage(sprintf(
                'ModflowModel with id: \'%s\' and BoundaryId: \'%s\' and ObservationPointId: %s not found.',
                $modelId->toString(),
                $boundaryId->toString(),
                $observationPointId->toString()
            ));
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
     * @throws \Inowas\ModflowBundle\Exception\NotFoundException
     */
    public function getModflowModelBoundaryObservationPointGeometryAction(string $id, string $bid, string $oid): Response
    {
        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);

        $this->assertUuidIsValid($bid);
        $boundaryId = BoundaryId::fromString($bid);

        $this->assertUuidIsValid($oid);
        $observationPointId = ObservationPointId::fromString($oid);

        $geometry = $this->get('inowas.modflowmodel.boundary_finder')->getBoundaryObservationPointGeometry($modelId, $boundaryId, $observationPointId);

        if (! $geometry instanceof Geometry){
            throw NotFoundException::withMessage(sprintf(
                'ModflowModel with id: \'%s\' and BoundaryId: \'%s\' and ObservationPointId: %s not found.',
                $modelId->toString(),
                $boundaryId->toString(),
                $observationPointId->toString()
            ));
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
     * @throws \Inowas\ModflowBundle\Exception\NotFoundException
     */
    public function getModflowModelBoundaryObservationPointValuesAction(string $id, string $bid, string $oid): Response
    {
        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);

        $this->assertUuidIsValid($bid);
        $boundaryId = BoundaryId::fromString($bid);

        $this->assertUuidIsValid($oid);
        $observationPointId = ObservationPointId::fromString($oid);

        $values = $this->get('inowas.modflowmodel.boundary_finder')->getBoundaryObservationPointValues($modelId, $boundaryId, $observationPointId);

        if (null === $values){
            throw NotFoundException::withMessage(sprintf(
                'ModflowModel with id: \'%s\' and BoundaryId: \'%s\' and ObservationPointId: %s not found.',
                $modelId->toString(),
                $boundaryId->toString(),
                $observationPointId->toString()
            ));
        }

        return new JsonResponse($values);
    }
}
