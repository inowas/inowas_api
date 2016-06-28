<?php

namespace AppBundle\Controller;

use AppBundle\Entity\GeologicalLayer;
use AppBundle\Entity\PropertyType;
use AppBundle\Model\Interpolation\BoundingBox;
use AppBundle\Model\Interpolation\GridSize;
use AppBundle\Model\Interpolation\KrigingInterpolation;
use AppBundle\Model\Interpolation\PointValue;
use AppBundle\Service\Interpolation;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class InterpolationRestController extends FOSRestController
{
    /**
     * Make an InterpolationRequest
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Make an InterpolationRequest",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @return View
     */
    public function postInterpolationAction()
    {
        
        $ki = new KrigingInterpolation(new GridSize(12, 13), new BoundingBox(1.2, 1.2, 2.1, .2));
        $ki->addPoint(new PointValue(1.1, 2.2, 3.4));
        $ki->addPoint(new PointValue(4.4, 5.5, 6.6));
        $serializer = $this->get('serializer');
        $serializedKi = $serializer->serialize($ki, 'json');

        $tempFolder = '/tmp/interpolation';

        $fs = new Filesystem();
        if (!$fs->exists('/tmp/interpolation')) {
            $fs->mkdir($tempFolder);
        }

        $uuid = Uuid::uuid4();
        $inputFile = $tempFolder.'/'.$uuid->toString();
        $fs->dumpFile($inputFile, $serializedKi);

        $scriptName="interpolationCalculation.py";
        $builder = new ProcessBuilder();
        $builder
            ->setPrefix('python')
            ->setArguments(array('-W', 'ignore', $scriptName, $inputFile))
            ->setWorkingDirectory($this->get('kernel')->getRootDir().'/../py/pyprocessing/interpolation')
        ;

        /** @var Process $process */
        $process = $builder
            ->getProcess();
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        #$test = json_decode($process->getOutput());
        #print

        $view = View::create();
        $view->setData("test")
            ->setStatusCode(200)
        ;

        return $view;
    }

    /**
     * Make an InterpolationRequest
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Make an InterpolationRequest",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @return View
     */
    public function getInterpolationsAction()
    {
        $modflowModel = $this->getDoctrine()->getRepository('AppBundle:ModFlowModel')
            ->findOneBy(array(
                'name' => 'ModFlowModel Scenario 2'
            ));

        if (!$modflowModel) {
            throw new NotFoundHttpException();
        }

        $soilModelService = $this->get('inowas.soilmodel');
        $soilModelService->loadModflowModelById($modflowModel->getId());
        $layers = $modflowModel->getSoilModel()->getGeologicalLayers();

        foreach ($layers as $layer)
        {
            $propertyTypes = $soilModelService->getAllPropertyTypesFromLayer($layer);
            /** @var PropertyType $propertyType */
            foreach ($propertyTypes as $propertyType){
                $soilModelService->interpolateLayerByProperty(
                    $layer,
                    $propertyType->getAbbreviation(),
                    array(Interpolation::TYPE_GAUSSIAN, Interpolation::TYPE_MEAN));
            }
        }


        $view = View::create();
        $view->setData("")
            ->setStatusCode(200)
        ;

        return $view;
    }

    /**
     * Interpolates Layerproperties by Id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Make an InterpolationRequest",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param $layerId
     * @param $propertyTypeAbbreviation
     * @return View
     */
    public function getLayerPropertytypeInterpolateAction($layerId, $propertyTypeAbbreviation)
    {

        $geologicalLayer = $this->getDoctrine()->getRepository('AppBundle:GeologicalLayer')
            ->findOneBy(array(
                'id' => $layerId
            ));

        if (!$geologicalLayer) {
            throw new NotFoundHttpException(sprintf('Layer with id=%s not found', $layerId));
        }

        $propertyType = $this->getDoctrine()->getRepository('AppBundle:PropertyType')
            ->findOneBy(array(
                'abbreviation' => $propertyTypeAbbreviation
            ));

        if (!$propertyType) {
            throw new NotFoundHttpException(sprintf('PropertyType with abbreviation=%s not found', $propertyTypeAbbreviation));
        }

        $soilModel = $this->getDoctrine()->getRepository('AppBundle:SoilModel')
            ->findByLayerId($layerId);

        if (!$soilModel) {
            throw new NotFoundHttpException(sprintf('There is no soilmodel associated the given layer id=%s.', $layerId));
        }

        $modflowModel = $this->getDoctrine()->getRepository('AppBundle:ModFlowModel')
            ->findByLayerId($soilModel->getId());

        if (!$modflowModel) {
            throw new NotFoundHttpException(sprintf('There is no ModflowModel associated the given soilmodel id=%s.', $soilModel->getId()));
        }

        $soilModelService = $this->get('inowas.soilmodel');
        $soilModelService->setModflowModel($modflowModel);
        $soilModelService->setSoilModel($soilModel);


        /** @var GeologicalLayer $layer */
        $layer = $soilModelService->interpolateLayerByProperty($geologicalLayer, $propertyTypeAbbreviation, Interpolation::TYPE_GAUSSIAN);

        $view = View::create();
        $view->setData($layer)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('modelobjectdetails')));

        return $view;
    }
}
