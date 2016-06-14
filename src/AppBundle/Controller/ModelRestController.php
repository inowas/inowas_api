<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ConstantHeadBoundary;
use AppBundle\Entity\GeneralHeadBoundary;
use AppBundle\Entity\GeologicalLayer;
use AppBundle\Entity\ModFlowModel;
use AppBundle\Entity\PropertyType;
use AppBundle\Entity\PropertyValue;
use AppBundle\Entity\Stream;
use AppBundle\Entity\Well;
use AppBundle\Exception\InvalidArgumentException;
use AppBundle\Model\RasterFactory;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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

        if (!$user) {
            throw $this->createNotFoundException('User with username '.$username.' not found.');
        }

        if ($this->getUser() === $user || $this->isGranted('ROLE_ADMIN'))
        {
            $models = $this->getDoctrine()
                ->getRepository('AppBundle:ModFlowModel')
                ->findBy(
                    array('owner' => $user),
                    array('id' => 'ASC')
                );
        } else
        {
            $models = $this->getDoctrine()
                ->getRepository('AppBundle:ModFlowModel')
                ->findBy(
                    array(
                        'owner' => $user,
                        'public' => true
                    ),
                    array('id' => 'ASC')
                );
        }

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
     * @param $modelId
     *
     * @return View
     */
    public function getModelAction($modelId)
    {

        if ($this->isGranted('ROLE_ADMIN'))
        {
            $model = $this->getDoctrine()
                ->getRepository('AppBundle:ModFlowModel')
                ->findOneBy(array(
                    'id' => $modelId
                ));
        } elseif ($this->isGranted('ROLE_USER'))
        {
            $model = $this->getDoctrine()
                ->getRepository('AppBundle:ModFlowModel')
                ->findOneBy(array(
                    'id' => $modelId,
                    'owner' => $this->getUser()
                ));
        } else
        {
            $model = $this->getDoctrine()
                ->getRepository('AppBundle:ModFlowModel')
                ->findOneBy(array(
                    'id' => $modelId,
                    'public' => true
                ));
        }

        if (!$model) {
            throw $this->createNotFoundException('Model not found.');
        }

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
     * @param $modelId
     * @param $layerNumber
     * @param $propertyAbbreviation
     * @QueryParam(name="_format", nullable=true, description="Image format, default png", default="png")
     *
     * @return View
     * @throws 
     */
    public function getModflowmodelLayerPropertyAction(ParamFetcher $paramFetcher, $modelId, $layerNumber, $propertyAbbreviation)
    {

        $model = $this->getDoctrine()->getRepository('AppBundle:ModFlowModel')
            ->findOneBy(array(
                'id' => $modelId
            ));

        if (!$model instanceof ModFlowModel) {
            throw new InvalidArgumentException('ModflowModel not available.');
        }

        if (!$model->hasSoilModel()){
            throw new InvalidArgumentException(sprintf('ModflowModel %s has no SoilModel already.', $modelId));
        }

        $soilModel = $model->getSoilModel();
        if (!$soilModel->hasGeologicalLayers()){
            throw new InvalidArgumentException(sprintf('ModflowModel %s has a Soilmodel without Layers.', $modelId));
        }

        /** @var GeologicalLayer $layer */
        $layer = $soilModel->getLayerByNumber($layerNumber);
        if (null == $layer){
            throw new InvalidArgumentException(sprintf('SoilModel has no Layer with layernumber %s.', $layerNumber));
        }

        /** @var PropertyType $propertyType */
        $propertyType = $this->getDoctrine()->getRepository('AppBundle:PropertyType')
            ->findOneBy(array(
                'abbreviation' => $propertyAbbreviation
            ));
        if (!$propertyType) {
            throw new InvalidArgumentException('PropertyType not available.');
        }

        $property = $layer->getPropertyByPropertyType($propertyType);
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

        $fileFormat = $paramFetcher->get('_format');
        $geoImageService = $this->get('inowas.geoimage');
        $geoImageService->createImageFromRaster($raster, $fileFormat);
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
     * @param $modelId
     *
     * @return View
     */
    public function getModflowmodelBoundariesAction($modelId)
    {
        if ($this->isGranted('ROLE_ADMIN'))
        {
            $model = $this->getDoctrine()
                ->getRepository('AppBundle:ModFlowModel')
                ->findOneBy(array(
                    'id' => $modelId
                ));
        } elseif ($this->isGranted('ROLE_USER'))
        {
            $model = $this->getDoctrine()
                ->getRepository('AppBundle:ModFlowModel')
                ->findOneBy(array(
                    'id' => $modelId,
                    'owner' => $this->getUser()
                ));
        } else
        {
            $model = $this->getDoctrine()
                ->getRepository('AppBundle:ModFlowModel')
                ->findOneBy(array(
                    'id' => $modelId,
                    'public' => true
                ));
        }

        if (!$model) {
            throw $this->createNotFoundException('Model not found.');
        }

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
     * @param $modelId
     *
     * @return View
     */
    public function getModflowmodelConstant_headAction($modelId)
    {
        if ($this->isGranted('ROLE_ADMIN'))
        {
            $model = $this->getDoctrine()
                ->getRepository('AppBundle:ModFlowModel')
                ->findOneBy(array(
                    'id' => $modelId
                ));
        } elseif ($this->isGranted('ROLE_USER'))
        {
            $model = $this->getDoctrine()
                ->getRepository('AppBundle:ModFlowModel')
                ->findOneBy(array(
                    'id' => $modelId,
                    'owner' => $this->getUser()
                ));
        } else
        {
            $model = $this->getDoctrine()
                ->getRepository('AppBundle:ModFlowModel')
                ->findOneBy(array(
                    'id' => $modelId,
                    'public' => true
                ));
        }

        if (!$model) {
            throw $this->createNotFoundException('Model not found.');
        }

        $rivers = array();
        $boundaries = $model->getBoundaries();
        foreach ($boundaries as $boundary) {
            if ($boundary instanceof ConstantHeadBoundary) {
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
     * @param $modelId
     *
     * @return View
     */
    public function getModflowmodelGeneral_headAction($modelId)
    {
        if ($this->isGranted('ROLE_ADMIN'))
        {
            $model = $this->getDoctrine()
                ->getRepository('AppBundle:ModFlowModel')
                ->findOneBy(array(
                    'id' => $modelId
                ));
        } elseif ($this->isGranted('ROLE_USER'))
        {
            $model = $this->getDoctrine()
                ->getRepository('AppBundle:ModFlowModel')
                ->findOneBy(array(
                    'id' => $modelId,
                    'owner' => $this->getUser()
                ));
        } else
        {
            $model = $this->getDoctrine()
                ->getRepository('AppBundle:ModFlowModel')
                ->findOneBy(array(
                    'id' => $modelId,
                    'public' => true
                ));
        }

        if (!$model) {
            throw $this->createNotFoundException('Model not found.');
        }

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
     * @param $modelId
     *
     * @return View
     */
    public function getModflowmodelWellsAction($modelId)
    {
        if ($this->isGranted('ROLE_ADMIN'))
        {
            $model = $this->getDoctrine()
                ->getRepository('AppBundle:ModFlowModel')
                ->findOneBy(array(
                    'id' => $modelId
                ));
        } elseif ($this->isGranted('ROLE_USER'))
        {
            $model = $this->getDoctrine()
                ->getRepository('AppBundle:ModFlowModel')
                ->findOneBy(array(
                    'id' => $modelId,
                    'owner' => $this->getUser()
                ));
        } else
        {
            $model = $this->getDoctrine()
                ->getRepository('AppBundle:ModFlowModel')
                ->findOneBy(array(
                    'id' => $modelId,
                    'public' => true
                ));
        }

        if (!$model) {
            throw $this->createNotFoundException('Model not found.');
        }

        $wells = array();
        $boundaries = $model->getBoundaries();
        foreach ($boundaries as $boundary) {
            if ($boundary instanceof Well) {
                $wells[] = $boundary;
            }
        }

        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modelobjectdetails');

        $view = View::create();
        $view->setData($wells)
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
     * @param $modelId
     *
     * @return View
     */
    public function getModflowmodelRiversAction($modelId)
    {
        if ($this->isGranted('ROLE_ADMIN'))
        {
            $model = $this->getDoctrine()
                ->getRepository('AppBundle:ModFlowModel')
                ->findOneBy(array(
                    'id' => $modelId
                ));
        } elseif ($this->isGranted('ROLE_USER'))
        {
            $model = $this->getDoctrine()
                ->getRepository('AppBundle:ModFlowModel')
                ->findOneBy(array(
                    'id' => $modelId,
                    'owner' => $this->getUser()
                ));
        } else
        {
            $model = $this->getDoctrine()
                ->getRepository('AppBundle:ModFlowModel')
                ->findOneBy(array(
                    'id' => $modelId,
                    'public' => true
                ));
        }

        if (!$model) {
            throw $this->createNotFoundException('Model not found.');
        }

        $rivers = array();
        $boundaries = $model->getBoundaries();
        foreach ($boundaries as $boundary) {
            if ($boundary instanceof Stream) {
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
     * @param $modelId
     *
     * @return View
     */
    public function getModflowmodelContentSummaryAction($modelId){

        $model = $this->getDoctrine()
            ->getRepository('AppBundle:ModFlowModel')
            ->findOneBy(array(
                'id' => $modelId
            ));

        if (!$model) {
            throw $this->createNotFoundException('Model not found.');
        }

        $area = $model->getArea();

        if (!$area) {
            throw $this->createNotFoundException('Area not found.');
        }

        $surface = $this->getDoctrine()->getRepository('AppBundle:Area')
            ->getAreaSurfaceById($area->getId());
        $area->setSurface($surface);

        $geojson = $this->getDoctrine()->getRepository('AppBundle:Area')
            ->getAreaPolygonIn4326($area->getId());

        $twig = $this->get('twig');
        $html = $twig->render(':inowas/model/modflow:summary.html.twig', array(
            'model' => $model
        ));

        $result['html'] = $html;
        $result['geojson'] = $geojson;

        $view = View::create();
        $view->setData($result)
            ->setStatusCode(200)
        ;

        return $view;
    }
}
