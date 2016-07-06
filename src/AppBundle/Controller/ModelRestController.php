<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ConstantHeadBoundary;
use AppBundle\Entity\GeneralHeadBoundary;
use AppBundle\Entity\GeologicalLayer;
use AppBundle\Entity\ModelScenario;
use AppBundle\Entity\ModFlowModel;
use AppBundle\Entity\PropertyType;
use AppBundle\Entity\PropertyValue;
use AppBundle\Entity\StreamBoundary;
use AppBundle\Entity\User;
use AppBundle\Entity\WellBoundary;
use AppBundle\Model\Point;
use AppBundle\Model\RasterFactory;
use AppBundle\Service\GeoImage;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ModelRestController extends FOSRestController
{
    /**
     * Return the overall project list from a user.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return the overall Project list from a user",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param $username
     * @return View
     */
    public function getUserModelsAction($username)
    {
        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findOneBy(array(
                'username' => $username
            ));

        if (! $user instanceof User) {
            throw $this->createNotFoundException('User with username '.$username.' not found.');
        }

        $models = $this->getDoctrine()
            ->getRepository('AppBundle:ModFlowModel')
            ->findBy(
                array('owner' => $user),
                array('id' => 'ASC')
            );

        $view = View::create();
        $view->setData($models)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('list'))
            )
        ;

        return $view;
    }

    /**
     * Return the project-information by id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return the project-information by id.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param $id
     *
     * @return View
     */
    public function getModelAction($id)
    {
        $model = $this->findModelById($id);
        
        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modeldetails');

        $view = View::create();
        $view->setData($model)
            ->setStatusCode(200)
            ->setSerializationContext($serializationContext)
        ;

        return $view;
    }

    /**
     * Return the ModflowModel Layer Property Image.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return the ModflowModel Layer Property Image.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param ParamFetcher $paramFetcher
     * @param $id
     * @param $layerNumber
     * @param $propertyAbbreviation
     * @QueryParam(name="_format", nullable=true, description="Image format, default png", default="png")
     *
     * @return View
     * @throws 
     */
    public function getModflowmodelLayerPropertyAction(ParamFetcher $paramFetcher, $id, $layerNumber, $propertyAbbreviation)
    {

        // Top-Elevation == Bottom elevation of the layer above
        if ($layerNumber >0 && $propertyAbbreviation == PropertyType::TOP_ELEVATION) {
            $layerNumber -=1;
            $propertyAbbreviation = PropertyType::BOTTOM_ELEVATION;
        }

        /** @var ModFlowModel $model */
        $model = $this->findModelById($id);

        if (!$model->hasSoilModel()){
            throw new NotFoundHttpException(sprintf('ModflowModel %s has no SoilModel already.', $model->getId()->toString()));
        }

        $soilModel = $model->getSoilModel();
        if (!$soilModel->hasGeologicalLayers()){
            throw new NotFoundHttpException(sprintf('ModflowModel %s has a Soilmodel without Layers.', $model->getId()->toString()));
        }

        /** @var GeologicalLayer $layer */
        $layer = $soilModel->getLayerByNumber($layerNumber);
        if (null == $layer){
            throw new NotFoundHttpException(sprintf('SoilModel has no Layer with layernumber %s.', $layerNumber));
        }

        /** @var PropertyType $propertyType */
        $propertyType = $this->getDoctrine()->getRepository('AppBundle:PropertyType')
            ->findOneBy(array(
                'abbreviation' => $propertyAbbreviation
            ));
        if (!$propertyType) {
            throw new NotFoundHttpException('PropertyType not available.');
        }

        $property = $layer->getPropertyByPropertyType($propertyType);

        if (null === $property){
            throw new \Exception(sprintf('Layer %s has no Property %s', $layerNumber, $propertyAbbreviation));
        }

        if (!$property->getValues()->count() == 1){
            throw new \Exception('Property has more then one value');
        }
        
        /** @var PropertyValue $propertyValue */
        $propertyValue = $property->getValues()->first();

        if ($propertyValue->hasRaster())
        {
            $raster = $propertyValue->getRaster();
        } elseif ($propertyValue->hasValue()) {
            $raster = RasterFactory::create()
                ->setGridSize($model->getGridSize())
                ->setBoundingBox($model->getBoundingBox());

            $data = array();
            $value = $propertyValue->getValue();
            for ($y = 0; $y<$model->getGridSize()->getNY(); $y++){
                $data[] = array_fill(0, $model->getGridSize()->getNX(), $value);
            }

            $raster->setData($data);
        } else {
            throw new \Exception('PropertyValue has no Value');
        }

        $colorScheme = GeoImage::COLOR_RELIEF_JET;
        $min = null; $max=null;
        if ($propertyType->getAbbreviation() == "hh") {
            $colorScheme = GeoImage::COLOR_RELIEF_GIST_RAINBOW;
            $min = -30;
            $max = 8;
        }

        $fileFormat = $paramFetcher->get('_format');
        $geoImageService = $this->get('inowas.geoimage');
        $geoImageService->createImageFromRaster($raster, $model->getActiveCells()->toArray(), $min, $max, $fileFormat, $colorScheme);
        $outputFileName = $geoImageService->getOutputFileName();

        $fs = new Filesystem();
        if (!$fs->exists($outputFileName)){
            throw new \Exception('Something went wrong creating the image');
        }

        $response = new BinaryFileResponse($outputFileName);
        switch( $fileFormat ) {
            case "png":  $hType="image/png"; break;
            case "tiff": $hType="image/tiff"; break;
            default:
                $hType = "";
        }

        $response->headers->set('Content-Type', $hType);
            return $response;
    }

    /**
     * Returns a list of all Boundaries by ModflowModel-Id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns a list of all Boundaries by ModflowModel-Id.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the ModflowModel is not found"
     *   }
     * )
     *
     * @param $id
     *
     * @return View
     */
    public function getModflowmodelBoundariesAction($id)
    {
        /** @var ModFlowModel $model */
        $model = $this->findModelById($id);
        $boundaries = $model->getBoundaries();

        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modelobjectdetails');

        $view = View::create();
        $view->setData($boundaries)
            ->setStatusCode(200)
            ->setSerializationContext($serializationContext)
        ;

        return $view;
    }

    /**
     * Returns a list of all Constant Head Boundaries by ModflowModel-Id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns a list of all Constant Head Boundaries by ModflowModel-Id.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the ModflowModel is not found"
     *   }
     * )
     *
     * @param ParamFetcher $paramFetcher
     * @param $id
     *
     * @QueryParam(name="geojson", nullable=true, description="Returns the geometry only as geojson", default=false)
     * @QueryParam(name="srid", nullable=true, description="The target srid, default is 4326", default=4326)
     * @return View
     */
    public function getModflowmodelConstant_headAction(ParamFetcher $paramFetcher, $id)
    {
        /** @var ModFlowModel $model */
        $model = $this->findModelById($id);

        $constantHeadBoundaries = array();
        $boundaries = $model->getBoundaries();
        foreach ($boundaries as $boundary) {
            if ($boundary instanceof ConstantHeadBoundary) {
                $constantHeadBoundaries[] = $boundary;
            }
        }

        if ($paramFetcher->get('geojson')){
            $srid = $paramFetcher->get('srid');
            $geoTools = $this->get('inowas.geotools');

            $geometries = array();
            /** @var ConstantHeadBoundary $boundary */
            foreach ($constantHeadBoundaries as $boundary) {
                $geometry = json_decode($geoTools->getGeometryFromModelObjectAsGeoJSON($boundary, $srid));
                $geometries[] = $geometry;

                foreach ($boundary->getObservationPoints() as $observationPoint) {
                    $geometry = json_decode($geoTools->getGeometryFromModelObjectAsGeoJSON($observationPoint, $srid));
                    $geometries[] = $geometry;
                }
            }

            return new Response(json_encode($geometries, true));
        }

        $serializationContext = SerializationContext::create();
        $serializationContext
            ->setGroups("boundarylist")
        ;

        $view = View::create();
        $view->setData($constantHeadBoundaries)
            ->setStatusCode(200)
            ->setSerializationContext($serializationContext)
        ;

        return $view;
    }

    /**
     * Returns a list of all General Head Boundaries by ModflowModel-Id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns a list of all General Head Boundaries by ModflowModel-Id.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the ModflowModel is not found"
     *   }
     * )
     *
     * @param $id
     *
     * @return View
     */
    public function getModflowmodelGeneral_headAction($id)
    {
        /** @var ModFlowModel $model */
        $model = $this->findModelById($id);

        $rivers = array();
        $boundaries = $model->getBoundaries();
        foreach ($boundaries as $boundary) {
            if ($boundary instanceof GeneralHeadBoundary) {
                $rivers[] = $boundary;
            }
        }

        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modelobjectdetails');

        $view = View::create();
        $view->setData($rivers)
            ->setStatusCode(200)
            ->setSerializationContext($serializationContext)
        ;

        return $view;
    }

    /**
     * Returns a list of all Wells by ModflowModel-Id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns a list of all Wells by ModflowModel-Id.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the ModflowModel is not found"
     *   }
     * )
     *
     * @param ParamFetcher $paramFetcher
     * @param $id
     * @QueryParam(name="srid", nullable=true, description="SRID, default 3857", default="3857")
     *
     * @return View
     */
    public function getModflowmodelWellsAction(ParamFetcher $paramFetcher, $id)
    {
        /** @var ModFlowModel $model */
        $model = $this->findModelById($id);
        $wells = array();
        $boundaries = $model->getBoundaries();
        foreach ($boundaries as $boundary) {
            if ($boundary instanceof WellBoundary) {
                $wells[] = $boundary;
            }
        }

        $targetSrid = (int)$paramFetcher->get('srid');
        /** @var WellBoundary $well */
        foreach ($wells as $well) {
            if ($well->getPoint()->getSrid() != $targetSrid) {
                $point = json_decode($this->getDoctrine()->getRepository('WellBoundary.php')
                    ->transformPointTo($well->getId(), $targetSrid));
                $well->setPoint(new Point($point->coordinates[0], $point->coordinates[1], $targetSrid));
            }
        }

        $response = array();
        /** @var WellBoundary $well */
        foreach ($wells as $well) {
            $response[$well->getWellType()][] = $well;
        }

        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modelobjectdetails');

        $view = View::create();
        $view->setData($response)
            ->setStatusCode(200)
            ->setSerializationContext($serializationContext)
        ;

        return $view;
    }

    /**
     * Returns a list of all Rivers by ModflowModel-Id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns a list of all Rivers by ModflowModel-Id.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the ModflowModel is not found"
     *   }
     * )
     *
     * @param ParamFetcher $paramFetcher
     * @param $id
     *
     * @QueryParam(name="geojson", nullable=true, description="Returns the geometry only as geojson", default=false)
     * @QueryParam(name="srid", nullable=true, description="The target srid, default is 4326", default=4326)
     * @return View
     */
    public function getModflowmodelRiversAction(ParamFetcher $paramFetcher, $id)
    {
        /** @var ModFlowModel $model */
        $model = $this->findModelById($id);

        $rivers = array();
        $boundaries = $model->getBoundaries();
        foreach ($boundaries as $boundary) {
            if ($boundary instanceof StreamBoundary) {
                $rivers[] = $boundary;
            }
        }

        if ($paramFetcher->get('geojson')){
            $srid = $paramFetcher->get('srid');
            $geoTools = $this->get('inowas.geotools');

            $geometries = array();
            /** @var ConstantHeadBoundary $boundary */
            foreach ($rivers as $boundary) {
                $geometry = json_decode($geoTools->getGeometryFromModelObjectAsGeoJSON($boundary, $srid));
                $geometries[] = $geometry;

                foreach ($boundary->getObservationPoints() as $observationPoint) {
                    $geometry = json_decode($geoTools->getGeometryFromModelObjectAsGeoJSON($observationPoint, $srid));
                    $geometries[] = $geometry;
                }
            }

            return new Response(json_encode($geometries, true));
        }

        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modelobjectdetails');

        $view = View::create();
        $view->setData($rivers)
            ->setStatusCode(200)
            ->setSerializationContext($serializationContext)
        ;

        return $view;
    }

    /**
     * Returns the boundingbox array from a model
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns the boundingbox array from a model.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the ModflowModel is not found"
     *   }
     * )
     *
     * @param ParamFetcher $paramFetcher
     * @param $id
     *
     * @QueryParam(name="srid", nullable=true, description="SRID, default 3857", default="3857")
     * @return View
     */
    public function getModflowmodelBoundingboxAction(ParamFetcher $paramFetcher, $id)
    {
        /** @var ModFlowModel $model */
        $model = $this->findModelById($id);

        if (null === $model->getBoundingBox()) {
            throw $this->createNotFoundException('BoundingBox not found.');
        }

        $srid = $paramFetcher->get('srid');
        $bb = $model->getBoundingBox();
        
        if ($bb->getSrid() != 0 && $bb->getSrid() != $srid){
            $bb = $this->getDoctrine()->getRepository('AppBundle:ModFlowModel')
                ->transformBoundingBox($model->getBoundingBox(), $srid);
        }

        $result = array(
            array($bb->getYMin(), $bb->getXMin()),
            array($bb->getYMax(), $bb->getXMax())
        );

        $view = View::create();
        $view->setData($result)
            ->setStatusCode(200)
        ;

        return $view;
    }

    /**
     * Returns the grid as geojson
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns the grid as geojson from a model.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the ModflowModel is not found"
     *   }
     * )
     *
     * @param ParamFetcher $paramFetcher
     * @param $id
     *
     * @QueryParam(name="srid", nullable=true, description="SRID, default 3857", default="3857")
     * @return View
     */
    public function getModflowmodelGridAction(ParamFetcher $paramFetcher, $id)
    {
        /** @var ModFlowModel $model */
        $model = $this->findModelById($id);

        if (null === $model->getBoundingBox()) {
            throw $this->createNotFoundException('BoundingBox not found.');
        }

        if (null === $model->getGridSize()) {
            throw $this->createNotFoundException('Gridsize not found.');
        }

        if (null === $model->getActiveCells()) {
            throw $this->createNotFoundException('ActiveCells not found.');
        }

        $srid = $paramFetcher->get('srid');
        $bb = $model->getBoundingBox();

        if ($bb->getSrid() != 0 && $bb->getSrid() != $srid){
            $bb = $this->getDoctrine()->getRepository('AppBundle:ModFlowModel')
                ->transformBoundingBox($model->getBoundingBox(), $srid);
        }

        $result = $this->get('inowas.geotools')->getGeoJsonGrid($bb, $model->getGridSize(), $model->getActiveCells());

        $view = View::create();
        $view->setData($result)
            ->setStatusCode(200)
        ;

        return $view;
    }

    /**
     * Returns the html content and polygon-data for the summary view by modflow-model-id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns the html content and polygon-data for the summary view by modflow-model-id.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the ModflowModel is not found"
     *   }
     * )
     *
     * @param $id
     * @param $contentType
     *
     * @return View
     */
    public function getModflowmodelContentAction($id, $contentType){

        /** @var ModFlowModel $model */
        $model = $this->findModelById($id);

        if ($contentType == 'summary')
        {
            $area = $model->getArea();
            if (!$area) {
                throw $this->createNotFoundException('Area not found.');
            }

            $surface = $this->getDoctrine()->getRepository('AppBundle:Area')
                ->getAreaSurfaceById($area->getId());
            $area->setSurface($surface);

            $geoJson = $this->getDoctrine()->getRepository('AppBundle:Area')
                ->getAreaPolygonIn4326($area->getId());

            $twig = $this->get('twig');
            $html = $twig->render(':inowas/model/modflow:summary.html.twig', array(
                'model' => $model
            ));

            $result['html'] = $html;
            $result['geojson'] = $geoJson;

        } elseif ($contentType == 'soilmodel') {
            if (!$model->hasSoilModel()){
                throw $this->createNotFoundException('Soilmodel not found.');
            }

            $geoJson = $this->getDoctrine()->getRepository('AppBundle:Area')
                ->getAreaPolygonIn4326($model->getArea()->getId());
            $layers = $model->getSoilModel()->getSortedGeologicalLayers();

            $twig = $this->get('twig');
            $html = $twig->render(':inowas/model/modflow:soilmodel.html.twig', array(
                'modelId' => $model->getId()->toString(),
                'layers' => $layers
            ));

            $result['html'] = $html;
            $result['geojson'] = $geoJson;
        } else {
            $result="";
        }


        $view = View::create();
        $view->setData($result)
            ->setStatusCode(200)
        ;

        return $view;
    }

    /**
     * @param $id
     * @return \AppBundle\Entity\AbstractModel
     */
    private function findModelById($id)
    {
        try {
            $id = Uuid::fromString($id);
        } catch (\InvalidArgumentException $e) {
            throw $this->createNotFoundException('Model with id='.$id.' not found.');
        }

        $scenario = $this->getDoctrine()
            ->getRepository('AppBundle:ModelScenario')
            ->findOneBy(array(
                'id' => $id
            ));

        if ($scenario instanceof ModelScenario) {
            return $scenario->getModel();
        }

        $model = $this->getDoctrine()
            ->getRepository('AppBundle:ModFlowModel')
            ->findOneBy(array(
                'id' => $id,
            ));

        if (!$model) {
            throw $this->createNotFoundException('Model not found.');
        }

        return $model;
    }
}
