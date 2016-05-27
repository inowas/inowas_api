<?php

namespace AppBundle\Service;

use AppBundle\Model\Interpolation\BoundingBox;
use AppBundle\Model\Interpolation\GridSize;
use AppBundle\Model\Interpolation\KrigingInterpolation;
use AppBundle\Model\Interpolation\MeanInterpolation;
use AppBundle\Model\Interpolation\PointValue;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Serializer;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class Interpolation
{
    const TYPE_KRIGING = 'kriging';
    const TYPE_MEAN = 'mean';

    /** @var array */
    private $availableTypes = [self::TYPE_KRIGING, self::TYPE_MEAN];

    /** @var string  */
    private $tmpFolder = '/tmp/interpolation';

    /** @var string  */
    protected $type;

    /** @var  GridSize */
    protected $gridSize;

    /** @var  BoundingBox */
    protected $boundingBox;

    /** @var array $data */
    protected $data;

    /** @var ArrayCollection PointValue */
    protected $points;

    /** @var  Serializer */
    protected $serializer;

    /** @var  KernelInterface */
    protected $kernel;

    /**
     * Interpolation constructor.
     * @param $serializer
     * @param $kernel
     */
    public function __construct($serializer, $kernel)
    {
        $this->type = self::TYPE_KRIGING;
        $this->points = new ArrayCollection();
        $this->serializer = $serializer;
        $this->kernel = $kernel;
    }

    /**
     * @param $type
     * @return $this
     */
    public function setType($type)
    {
        if (in_array($type, $this->availableTypes)) {
            $this->type = $type;
        }

        return $this;
    }

    /**
     * @param GridSize $gridSize
     * @return $this
     */
    public function setGridSize(GridSize $gridSize)
    {
        $this->gridSize = $gridSize;
        return $this;
    }

    /**
     * @return GridSize
     */
    public function getGridSize()
    {
        return $this->gridSize;
    }

    /**
     * @param BoundingBox $boundingBox
     * @return $this
     */
    public function setBoundingBox(BoundingBox $boundingBox)
    {
        $this->boundingBox = $boundingBox;
        return $this;
    }

    /**
     * @return BoundingBox
     */
    public function getBoundingBox()
    {
        return $this->boundingBox;
    }


    public function addPoint(PointValue $pointValue)
    {
        if (!$this->points->contains($pointValue))
        {
            $this->points[] = $pointValue;
        }
    }

    public function removePoint(PointValue $pointValue)
    {
        if ($this->points->contains($pointValue))
        {
            $this->points->remove($pointValue);
        }
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }



    public function interpolate()
    {
        unset($this->data);

        if ($this->type == 'kriging')
        {
            $ki = new KrigingInterpolation($this->gridSize, $this->boundingBox, $this->points);
            $serializedKi = $this->serializer->serialize($ki, 'json');

            $fs = new Filesystem();
            if (!$fs->exists($this->tmpFolder)) {
                $fs->mkdir($this->tmpFolder);
            }

            $uuid = Uuid::uuid4();
            $inputFile = $this->tmpFolder.'/'.$uuid->toString();
            $fs->dumpFile($inputFile, $serializedKi);

            $scriptName="interpolationCalculation.py";
            $builder = new ProcessBuilder();
            $builder
                ->setPrefix('python')
                ->setArguments(array('-W', 'ignore', $scriptName, $inputFile))
                ->setWorkingDirectory($this->kernel->getRootDir().'/../py/pyprocessing/interpolation')
            ;

            /** @var Process $process */
            $process = $builder
                ->getProcess();
            $process->run();
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            $jsonResponse = $process->getOutput();
            $response = json_decode($jsonResponse);

            $this->data = $response->raster;
        }

        if ($this->type == 'mean')
        {
            $ki = new MeanInterpolation($this->gridSize, $this->boundingBox, $this->points);
            $serializedKi = $this->serializer->serialize($ki, 'json');

            $fs = new Filesystem();
            if (!$fs->exists($this->tmpFolder)) {
                $fs->mkdir($this->tmpFolder);
            }

            $uuid = Uuid::uuid4();
            $inputFile = $this->tmpFolder.'/'.$uuid->toString();
            $fs->dumpFile($inputFile, $serializedKi);

            $scriptName="interpolationCalculation.py";
            $builder = new ProcessBuilder();
            $builder
                ->setPrefix('python')
                ->setArguments(array('-W', 'ignore', $scriptName, $inputFile))
                ->setWorkingDirectory($this->kernel->getRootDir().'/../py/pyprocessing/interpolation')
            ;

            /** @var Process $process */
            $process = $builder
                ->getProcess();
            $process->run();
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            $jsonResponse = $process->getOutput();
            $response = json_decode($jsonResponse);
            $this->data = $response->raster;
        }

        return 0;

    }

}