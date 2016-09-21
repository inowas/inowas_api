<?php

namespace AppBundle\Controller;

use AppBundle\Entity\GeologicalLayer;
use AppBundle\Entity\ModelScenario;
use AppBundle\Entity\ModflowCalculation;
use AppBundle\Entity\ModFlowModel;
use AppBundle\Entity\PropertyValue;
use AppBundle\Entity\User;
use AppBundle\Model\ActiveCells;
use AppBundle\Model\AreaFactory;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\GridSize;
use AppBundle\Model\ModFlowModelFactory;
use AppBundle\Model\PropertyType;
use AppBundle\Model\PropertyTypeFactory;
use AppBundle\Model\RasterFactory;
use AppBundle\Model\SoilModelFactory;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use Inowas\PyprocessingBundle\Model\GeoImage\GeoImageParameter;
use Inowas\PyprocessingBundle\Model\Modflow\Package\FlopyCalculationPropertiesFactory;
use Inowas\PyprocessingBundle\Service\Flopy;
use Inowas\PyprocessingBundle\Service\GeoImage;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ModflowModelRestController extends FOSRestController
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
    public function getUserModflowmodelsAction($username)
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
    public function getModflowmodelsAction($id)
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
     * Deletes a ModflowModel by id.
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
     * @return Response
     */
    public function deleteModflowmodelsAction($id)
    {
        $model = $this->findModelById($id);

        if (! $this->getUser() == $model->getOwner()){
            throw new AccessDeniedException('To delete the model you have to be the owner.');
        }

        $this->getDoctrine()->getManager()->remove($model);
        $this->getDoctrine()->getManager()->flush();

        return new Response('Success');
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
     * @param Request $request
     * @return View
     * @todo make the code testable
     */
    public function postModflowmodelAction(Request $request){

        $data = json_decode($request->request->get('json'), true);

        $model = ModFlowModelFactory::create()
            ->setName($data['name'])
            ->setDescription($data['description'])
            ->setGridSize(new GridSize($data['grid_size']['cols'], $data['grid_size']['rows']))
            ;

        $soilModel = SoilModelFactory::create();
        for ($i = 0; $i < $data['soil_model']['numberOfLayers']; $i++){
            $soilModel->addGeologicalLayer(GeologicalLayerFactory::create()
            ->setName('Layer'.($i+1))
            ->setOrder($i));
        }
        $model->setSoilModel($soilModel);

        $area = AreaFactory::create()
            ->setName("")
            ->setGeometry(new Polygon(json_decode($data['area']['geoJSON'])->geometry->coordinates, 4326))
        ;

        $model->setArea($area);
        $model->setBoundingBox($this->get('inowas.geotools')->getBoundingBoxFromPolygon($area->getGeometry()));

        $this->getDoctrine()->getManager()->persist($model);
        $this->getDoctrine()->getManager()->flush();

        $activeCells = $this->get('inowas.geotools')->getActiveCells($model->getArea(), $model->getBoundingBox(), $model->getGridSize());
        $model->setActiveCells($activeCells);

        $this->getDoctrine()->getManager()->persist($model);
        $this->getDoctrine()->getManager()->flush();

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
     * Update ModflowModelData by corresponding model-id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update ModflowModelData by corresponding model-id.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @RequestParam(name="active_cells", description="Active cells array.", strict=false)
     *
     * @param ParamFetcher $paramFetcher
     * @param $id
     * @return View
     */
    public function putModflowmodelAction(ParamFetcher $paramFetcher, $id){
        /** @var ModFlowModel $model */
        $model = $this->findModelById($id);

        if ($paramFetcher->get('active_cells')){
            $model->setActiveCells(ActiveCells::fromJSON($paramFetcher->get('active_cells')));
        }

        $this->getDoctrine()->getManager()->persist($model);
        $this->getDoctrine()->getManager()->flush();

        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modelProperties');

        $view = View::create();
        $view->setData($model)
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
     * @QueryParam(name="srid", nullable=true, description="SRID, default 3857", default=3857)
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
     * Returns the gridsize, boundingbox, activecells array from a model
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
     * @param $id
     *
     * @return View
     */
    public function getModflowmodelPropertiesAction($id){

        /** @var ModFlowModel $model */
        $model = $this->findModelById($id);

        $view = View::create();
        $view->setData($model)
            ->setSerializationContext(
                SerializationContext::create()
                    ->setGroups('modelProperties'))
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

        if ($contentType == 'summary') {
            $area = $model->getArea();
            if (!$area) {
                throw $this->createNotFoundException('Area not found.');
            }

            $surface = $this->getDoctrine()->getRepository('AppBundle:Area')
                ->getAreaSurfaceById($area->getId());

            if ($surface > 100000){
                $surface = round($surface/1000000, 1). ' sqkm';
            } else (
            $surface = round($surface). ' sqm'
            );

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
        } elseif ($contentType == 'calculation') {

            $calculations = $this->getDoctrine()->getRepository('AppBundle:ModflowCalculation')
                ->findBy(
                    array('modelId' => $id),
                    array('dateTimeAddToQueue' => 'DESC'),
                    1
                );

            $calculation = null;
            if (count($calculations) > 0){
                $calculation = $calculations[0];
            }

            $twig = $this->get('templating');
            $html = $twig->renderResponse(':inowas/model/modflow:calculation.html.twig', array(
                'calculation' => $calculation
            ))->getContent();

            $result['html'] = $html;

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
     * Get head values
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Post head values.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the Calculation-Id is not found"
     *   }
     * )
     *
     * @param $id
     * @return View
     *
     * @QueryParam(name="totim", description="Time in days from beginning")
     */
    public function getModflowmodelHeadsAction($id)
    {
        /** @var ModFlowModel $model */
        $element = $this->findElementById($id);

        $heads = null;
        $model = null;

        if ($element instanceof ModFlowModel){
            $heads = $element->getHeads();
            $model = $element;
        }

        if ($element instanceof ModelScenario){
            $heads = $element->getHeads();
            $model = $element->getBaseModel();
        }

        $response = array();
        if ($model->getStressPeriods()->count() > 0){
            /** @var \DateTime $startDate */
            $startDate = $model->getStressPeriods()->first()->getDateTimeBegin();

            foreach ($heads as $totim => $head){
                $date = clone $startDate;
                $date->modify(sprintf('+%s days', (int)$totim-1));
                $response[$date->format('Y-m-d')] = $head;
            }
        }

        $view = View::create();
        $view->setData($response)
            ->setStatusCode(200)
        ;

        return $view;
    }

    /**
     * Route to submit Result Head-Values
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Post head values.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the Calculation-Id is not found"
     *   }
     * )
     *
     * @param $id
     * @param ParamFetcher $paramFetcher
     * @return View
     *
     * @RequestParam(name="totim", description="Time in days from beginning")
     * @RequestParam(name="heads", description="Heads-Array in Json")
     */
    public function postModflowmodelHeadsAction($id, ParamFetcher $paramFetcher)
    {
        $totim = $paramFetcher->get('totim');
        $head = $paramFetcher->get('heads');

        /** @var ModFlowModel $model */
        $model = $this->findElementById($id);
        $heads = $model->getHeads();
        $heads[$totim] = $head;

        $model->setHeads($heads);
        $this->getDoctrine()->getManager()->persist($model);
        $this->getDoctrine()->getManager()->flush();

        $view = View::create();
        $view->setData('OK')
            ->setStatusCode(200)
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
     * @return Response
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
        $propertyType = PropertyTypeFactory::create($propertyAbbreviation);
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
        $min = 290; $max = 480;
        if ($propertyType->getAbbreviation() == "hh") {
            $colorScheme = GeoImage::COLOR_RELIEF_GIST_RAINBOW;
            $min = -30;
            $max = 8;
        }

        $fileFormat = $paramFetcher->get('_format');
        $geoImageService = $this->get('inowas.geoimage');
        $geoImageParameter = new GeoImageParameter($raster, $model->getActiveCells()->toArray(), $min, $max, $fileFormat, $colorScheme);
        $geoImageService->createImage($geoImageParameter);
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
     * Returns state of calculation of the model by model-id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns state of calculation of the model by calculation-id.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the Calculation-Id is not found"
     *   }
     * )
     *
     * @param $id
     *
     * @return View
     */
    public function getModflowmodelCalculationsAction($id){

        $model = $this->findElementById($id);
        $calculations = $this->getDoctrine()->getRepository('AppBundle:ModflowCalculation')
            ->findBy(
                array('modelId' => $model->getId()->toString()),
                array('dateTimeAddToQueue' => 'DESC'),
                1
            );

        if (count($calculations)>0){
            $calculations = $calculations[0];
        }

        $view = View::create();
        $view->setData($calculations)
            ->setStatusCode(200)
        ;

        return $view;
    }

    /**
     * Sends the command to calculate the model by model-id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Sends the command to calculate the model by model-id",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the Calculation-Id is not found"
     *   }
     * )
     *
     * @param $id
     *
     * @return Response
     */
    public function postModflowmodelCalculationAction($id)
    {

        if ($this->isScenario($id))
        {
            $scenario = $this->getDoctrine()->getRepository('AppBundle:ModelScenario')
                ->findOneBy(array(
                    'id' => $id
                ));

            /** @var ModFlowModel $model */
            $model = $scenario->getBaseModel();

            $fpc = FlopyCalculationPropertiesFactory::loadFromApiRunAndSubmit($model);
            $model->setCalculationProperties($fpc);
            $this->getDoctrine()->getManager()->persist($model);
            $this->getDoctrine()->getManager()->flush();

            /** @var Flopy $flopy */
            $flopy = $this->get('inowas.flopy');

            $flopy->addToQueue(
                $this->getParameter('inowas.api_base_url'),
                $this->getParameter('inowas.modflow.data_folder'),
                $scenario->getId()->toString(),
                $this->getUser()->getId()->toString()
            );

            $flopy->startAsyncFlopyProcessRunner(
                $this->get('kernel')->getRootDir()
            );

            return $this->redirect(
                $this->generateUrl('get_modflowmodel_calculations', array('id' => $scenario->getId()->toString())).'.json'
            );
        }

        /** @var ModFlowModel $model */
        $model = $this->findModelById($id);

        $fpc = FlopyCalculationPropertiesFactory::loadFromApiRunAndSubmit($model);
        $model->setCalculationProperties($fpc);
        $this->getDoctrine()->getManager()->persist($model);
        $this->getDoctrine()->getManager()->flush();

        /** @var Flopy $flopy */
        $flopy = $this->get('inowas.flopy');

        $flopy->addToQueue(
            $this->getParameter('inowas.api_base_url'),
            $this->getParameter('inowas.modflow.data_folder'),
            $model->getId()->toString(),
            $this->getUser()->getId()->toString()
        );

        $flopy->startAsyncFlopyProcessRunner(
            $this->get('kernel')->getRootDir()
        );

        return $this->redirect(
            $this->generateUrl('get_modflowmodel_calculations', array('id' => $model->getId()->toString())).'.json'
        );

    }

    /**
     * @param $id
     * @return \AppBundle\Entity\AbstractModel
     */
    private function findElementById($id)
    {

        if (!Uuid::isValid($id)){
            throw $this->createNotFoundException(sprintf('Element with id: %s not found.', $id));
        }

        $element = $this->getDoctrine()
            ->getRepository('AppBundle:ModelScenario')
            ->findOneBy(array(
                'id' => $id
            ));

        if ($element instanceof ModelScenario) {
            return $element;
        }

        $element = $this->getDoctrine()
            ->getRepository('AppBundle:ModFlowModel')
            ->findOneBy(array(
                'id' => $id,
            ));

        if (! $element instanceof ModFlowModel) {
            throw $this->createNotFoundException(sprintf('Element with id: %s not found', $id));
        }

        return $element;
    }

    /**
     * @param $id
     * @return \AppBundle\Entity\AbstractModel
     */
    private function findModelById($id)
    {

        if (!Uuid::isValid($id)){
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

    private function isScenario($id){
        return $this->getDoctrine()->getRepository('AppBundle:ModelScenario')
            ->findOneBy(array(
                'id' => $id
            ));
    }
}
