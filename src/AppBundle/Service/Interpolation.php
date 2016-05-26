<?php

namespace AppBundle\Service;

use AppBundle\Model\Interpolation\BoundingBox;
use AppBundle\Model\Interpolation\GridSize;
use AppBundle\Model\Interpolation\KrigingInterpolation;
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
    private $availableTypes = ['kriging'];

    protected $tmpFolder = '/tmp/interpolation';

    /** @var string  */
    protected $type;

    /** @var  GridSize */
    protected $gridSize;

    /** @var  BoundingBox */
    protected $boundingBox;

    /** @var ArrayCollection PointValue */
    protected $points;

    /** @var  Serializer */
    protected $serializer;

    /** @var  KernelInterface */
    protected $kernel;

    public function __construct($serializer, $kernel)
    {
        $this->type = 'kriging';
        $this->points = new ArrayCollection();
        $this->serializer = $serializer;
        $this->kernel = $kernel;
    }

    public function setType($type)
    {
        if (in_array($type, $this->availableTypes))
        {
            $this->type = $type;
        }
    }

    public function setGridSize(GridSize $gridSize)
    {
        $this->gridSize = $gridSize;
    }

    public function setBoundingBox(BoundingBox $boundingBox)
    {
        $this->boundingBox = $boundingBox;
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

    public function interpolate()
    {
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

            return $process->getOutput();
        }

        return 0;

    }

}