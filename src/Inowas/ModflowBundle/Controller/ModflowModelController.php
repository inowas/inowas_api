<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Inowas\AppBundle\Model\User;
use Inowas\Common\Boundaries\Area;
use Inowas\Common\Boundaries\BoundaryName;
use Inowas\Common\Boundaries\ConstantHeadBoundary;
use Inowas\Common\Boundaries\GeneralHeadBoundary;
use Inowas\Common\Boundaries\RechargeBoundary;
use Inowas\Common\Boundaries\RiverBoundary;
use Inowas\Common\Boundaries\WellBoundary;
use Inowas\Common\Boundaries\WellType;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Geometry\Polygon;
use Inowas\Common\Grid\AffectedLayers;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\Modelname;
use Inowas\Common\Modflow\ModflowModelDescription;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\ModflowModel\Model\Command\AddBoundary;
use Inowas\ModflowModel\Model\Command\ChangeModflowModelDescription;
use Inowas\ModflowModel\Model\Command\ChangeModflowModelGridSize;
use Inowas\ModflowModel\Model\Command\ChangeModflowModelName;
use Inowas\ModflowModel\Model\Command\CreateModflowModel;
use Inowas\ModflowModel\Model\Command\UpdateAreaGeometry;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
     * @Rest\Get("modflowmodels")
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
            $this->get('inowas.modflowmodel.model_finder')->findByBaseUserId($userId)
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
     * @Rest\Get("modflowmodels/public")
     * @return JsonResponse
     */
    public function getPublicModflowModelsAction(): JsonResponse
    {
        return new JsonResponse(
            $this->get('inowas.modflowmodel.model_finder')->findPublic()
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
        $modflowModelId = ModflowId::generate();
        $name = Modelname::fromString($content['name']);
        $description = ModflowModelDescription::fromString($content['description']);

        $areaGeometry = new Polygon($content['area_geometry']['coordinates'], 4326);
        $area = Area::create(BoundaryId::generate(), BoundaryName::fromString($name->toString().' Area'), $areaGeometry);


        $gridSize = GridSize::fromXY($content['grid_size']['n_x'], $content['grid_size']['n_y']);
        $timeUnit = TimeUnit::fromString($content['time_unit']);
        $lengthUnit = LengthUnit::fromString($content['length_unit']);

        $commandBus = $this->get('prooph_service_bus.modflow_command_bus');
        $commandBus->dispatch(CreateModflowModel::newWithIdAndUnits($userId, $modflowModelId, $area, $gridSize, $timeUnit, $lengthUnit));
        $commandBus->dispatch(ChangeModflowModelName::forModflowModel($userId, $modflowModelId, $name));
        $commandBus->dispatch(ChangeModflowModelDescription::forModflowModel($userId, $modflowModelId, $description));

        return new RedirectResponse(
            $this->generateUrl('get_modflow_model', array('id' => $modflowModelId->toString())),
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
     * @Rest\Get("modflowmodels/{id}")
     * @return JsonResponse
     */
    public function getModflowModelAction(string $id): JsonResponse
    {
        $modelId = ModflowId::fromString($id);
        return new JsonResponse(
            $this->get('inowas.modflowmodel.model_finder')->findByModelId($modelId)
        );
    }

    /**
     * Updates data of existing ModflowModel by Id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Updates data of existing ModflowModel by Id.",
     *   statusCodes = {
     *     302 = "Redirect"
     *   }
     * )
     *
     * @param string $id
     * @param Request $request
     * @Rest\Put("modflowmodels/{id}")
     * @return RedirectResponse
     */
    public function putModflowModelAction(string $id, Request $request): RedirectResponse
    {
        $commandBus = $this->get('prooph_service_bus.modflow_command_bus');

        $userId = UserId::fromString($this->getUser()->getId()->toString());
        $modflowModelId = ModflowId::fromString($id);

        $content = $this->getContentAsArray($request);

        if (array_key_exists('name', $content)){
            $name = Modelname::fromString($content['name']);
            $commandBus->dispatch(ChangeModflowModelName::forModflowModel($userId, $modflowModelId, $name));
        }

        if (array_key_exists('description', $content)){
            $description = ModflowModelDescription::fromString($content['description']);
            $commandBus->dispatch(ChangeModflowModelDescription::forModflowModel($userId, $modflowModelId, $description));
        }

        if (array_key_exists('area_geometry', $content)){
            $areaGeometry = new Polygon($content['area_geometry']['coordinates'], 4326);
            $commandBus->dispatch(UpdateAreaGeometry::of($userId, $modflowModelId, $areaGeometry));
        }

        if (array_key_exists('grid_size', $content)){
            $gridSize = GridSize::fromXY($content['grid_size']['n_x'], $content['grid_size']['n_y']);
            $commandBus->dispatch(ChangeModflowModelGridSize::forModflowModel($userId, $modflowModelId, $gridSize));
        }

        return new RedirectResponse(
            $this->generateUrl('get_modflow_model', array('id' => $modflowModelId->toString())),
            302
        );
    }

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
     * @Rest\Get("modflowmodels/{id}/boundaries")
     * @return JsonResponse
     */
    public function getModflowModelBoundariesAction(string $id): JsonResponse
    {
        $modelId = ModflowId::fromString($id);
        $boundaries = $this->get('inowas.modflowmodel.boundaries_finder')->findByModelId($modelId);

        $response = array();
        foreach ($boundaries as $boundary){
            $response[] = (object)[
                'id' => $boundary->id,
                'name' => $boundary->name,
                'type' => $boundary->type,
                'geometry' => json_decode($boundary->geometry),
                'metadata' => json_decode($boundary->metadata)
            ];
        }

        return new JsonResponse($response);
    }

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
     * @param Request $request
     * @Rest\Post("modflowmodels/{id}/boundaries")
     * @return Response
     */
    public function postModflowModelBoundariesAction(string $id, Request $request): Response
    {
        $modelId = ModflowId::fromString($id);
        $content = $this->getContentAsArray($request);

        $userId = UserId::fromString($this->getUser()->getId()->toString());
        $boundaryId = BoundaryId::generate();

        if (! array_key_exists('type', $content)){
            return new Response('BoundaryType not given', 422);
        }

        $boundaryType = $content['type'];

        if (! array_key_exists('name', $content)){
            return new Response('BoundaryName not given', 422);
        }
        $name = BoundaryName::fromString($content['name']);

        if (! array_key_exists('geometry', $content)){
            return new Response('BoundaryGeometry not given', 422);
        }
        $geometry = Geometry::fromArray($content['geometry']);

        $boundary = null;
        switch ($boundaryType) {
            case "chd":

                if (! array_key_exists('affected_layers', $content)){
                    return new Response('AffectedLayers not given', 422);
                }
                $affectedLayers = AffectedLayers::createWithLayerNumbers($content['affected_layers']);

                $boundary = ConstantHeadBoundary::createWithParams(
                    $boundaryId,
                    $name,
                    $geometry,
                    $affectedLayers
                );
                break;
            case "ghb":

                if (! array_key_exists('affected_layers', $content)){
                    return new Response('AffectedLayers not given', 422);
                }
                $affectedLayers = AffectedLayers::createWithLayerNumbers($content['affected_layers']);

                $boundary = GeneralHeadBoundary::createWithParams(
                    $boundaryId,
                    $name,
                    $geometry,
                    $affectedLayers
                );
                break;
            case "rch":
                $boundary = RechargeBoundary::createWithParams(
                    $boundaryId,
                    $name,
                    $geometry
                );
                break;
            case "riv":
                $boundary = RiverBoundary::createWithParams(
                    $boundaryId,
                    $name,
                    $geometry
                );
                break;
            case "wel":

                if (! array_key_exists('affected_layers', $content)){
                    return new Response('AffectedLayers not given', 422);
                }
                $affectedLayers = AffectedLayers::createWithLayerNumbers($content['affected_layers']);
                $wellType = null;
                if (array_key_exists('metadata', $content)){
                    $metaData = json_decode($content['metadata'], true);
                    if (array_key_exists('well_type', $metaData)){
                        $wellType = WellType::fromString($metaData['well_type']);
                    }
                }

                if (null === $wellType){
                    $wellType = WellType::fromString(WellType::TYPE_PUBLIC_WELL);
                }

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
            return new Response('BoundaryType not known', 422);
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
     * @Rest\Get("modflowmodels/{id}/boundaries/{bid}")
     * @return Response
     */
    public function getModflowModelBoundaryAction(string $id, string $bid): Response
    {
        $modelId = ModflowId::fromString($id);
        $boundaryId = BoundaryId::fromString($bid);
        $boundaryDetails = $this->get('inowas.modflowmodel.boundaries_finder')->getBoundaryDetails($modelId, $boundaryId);

        if (false === $boundaryDetails){
            return new Response(sprintf('Boundary with id %s in Model with id %s not found.', $boundaryId, $modelId), 404);
        }

        return new JsonResponse($boundaryDetails);
    }
}
