<?php

namespace AppBundle\Controller;

use AppBundle\Model\Interpolation\BoundingBox;
use AppBundle\Model\Interpolation\GridSize;
use AppBundle\Model\Interpolation\KrigingInterpolation;
use AppBundle\Model\Interpolation\PointValue;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Filesystem\Filesystem;
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

        $test = json_decode($process->getOutput());

        print

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
        
        #$interpolation = $this->get('inowas.interpolation');
        #$interpolation->setType('kriging');
        #$interpolation->setGridSize(new GridSize(12,13));
        #$interpolation->setBoundingBox(new BoundingBox(1.2, 1.2, 2.1, .2));
        #$interpolation->addPoint(new PointValue(1.1, 2.2, 3.4));
        #$interpolation->addPoint(new PointValue(4.4, 5.5, 6.6));

        #$interpolation->interpolate();
        #$result = new Result($interpolation->getGridSize(), $interpolation->getBoundingBox(), $interpolation->getData());

        #$view = View::create();
        #$view->setData($result)
        #    ->setStatusCode(200)
        #;

        $modflowModelTools = $this->get('inowas.modflow');
        $modflowModelTools->loadModflowModelById('4d981078-e712-42f6-bf1d-8ef605663f8b');
        dump($modflowModelTools->getModflowModel()->getArea()->getBoundingBox());


        $view = View::create();
        $view->setData("")
            ->setStatusCode(200)
        ;

        return $view;
    }
}
