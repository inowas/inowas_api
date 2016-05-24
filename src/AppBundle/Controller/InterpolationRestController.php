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

        $scriptName="interpolation.py";
        $builder = new ProcessBuilder();
        $builder
            ->setPrefix('python')
            ->setArguments(array('-W', 'ignore', $scriptName, $inputFile))
            ->setWorkingDirectory($this->get('kernel')->getRootDir().'/../py/wps/')
        ;

        /** @var Process $process */
        $process = $builder
            ->getProcess();
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        echo $process->getOutput();


        #$serializer = $this->get('serializer');
        #$serializedKi = $serializer->serialize($ki, 'json');
        #$content = $this->render(':inowas/WPS:interpolation.xml.twig', array(
        #    'jsonData' => $serializedKi
        #));

        $view = View::create();
        $view->setData($ki)
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
        $ki = new KrigingInterpolation(new GridSize(12, 13), new BoundingBox(1.2, 1.2, 2.1, .2));
        $ki->addPoint(new PointValue(1.1, 2.2, 3.4));
        $ki->addPoint(new PointValue(4.4, 5.5, 6.6));
        $serializer = $this->get('serializer');
        $serializedKi = $serializer->serialize($ki, 'json');
        $content = $this->render(':inowas/WPS:interpolation.xml.twig', array(
            'jsonData' => $serializedKi
        ));


        return $content;
    }
}
