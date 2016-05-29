<?php

namespace AppBundle\Service;

use AppBundle\Model\Interpolation\BoundingBox;
use AppBundle\Model\Interpolation\GridSize;
use AppBundle\Model\Interpolation\PointValue;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Serializer;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Interpolation
{
    const TYPE_KRIGING = 'kriging';
    const TYPE_MEAN = 'mean';
    const TYPE_GAUSSIAN = 'gaussian';

    /** @var array */
    private $availableTypes = [self::TYPE_KRIGING, self::TYPE_MEAN, self::TYPE_GAUSSIAN];

    /** @var string  */
    private $tmpFolder = '/tmp/interpolation';

    /** @var string  */
    private $tmpFileName = '';

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

    /** @var  PythonProcess $pythonProcess */
    protected $pythonProcess;

    /**
     * Interpolation constructor.
     * @param $serializer
     * @param $kernel
     * @param $pythonProcess
     */
    public function __construct($serializer, $kernel, $pythonProcess)
    {
        $this->points = new ArrayCollection();
        $this->serializer = $serializer;
        $this->kernel = $kernel;
        $this->pythonProcess = $pythonProcess;
        $this->tmpFileName = Uuid::uuid4()->toString();
    }
    
    /**
     * @return string
     */
    public function getTmpFolder()
    {
        return $this->tmpFolder;
    }

    /**
     * @param string $tmpFolder
     * @return Interpolation
     */
    public function setTmpFolder($tmpFolder)
    {
        $this->tmpFolder = $tmpFolder;
        return $this;
    }

    /**
     * @return string
     */
    public function getTmpFileName()
    {
        return $this->tmpFileName;
    }

    /**
     * @param string $tmpFileName
     * @return Interpolation
     */
    public function setTmpFileName($tmpFileName)
    {
        $this->tmpFileName = $tmpFileName;
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
        if (!$this->points->contains($pointValue)){
            $this->points[] = $pointValue;
        }
    }

    public function removePoint(PointValue $pointValue)
    {
        if ($this->points->contains($pointValue)) {
            $this->points->removeElement($pointValue);
        }
    }

    /**
     * @return ArrayCollection
     */
    public function getPoints()
    {
        return $this->points;
    }

    public function interpolate($algorithm)
    {
        if (!in_array($algorithm, $this->availableTypes)) {
            throw new NotFoundHttpException(sprintf('Algorithm %s not found.', $algorithm));
        }

        if (!$this->gridSize instanceof GridSize) {
            throw new NotFoundHttpException('GridSize not set.');
        }

        if (!$this->boundingBox instanceof BoundingBox) {
            throw new NotFoundHttpException('BoundingBox not set.');
        }

        if ($this->points->count() == 0) {
            throw new NotFoundHttpException('No PointValues set.');
        }

        unset($this->data);

        $class = 'AppBundle\Model\Interpolation\\'.ucfirst($algorithm).'Interpolation';
        $interpolation = new $class($this->gridSize, $this->boundingBox, $this->points);
        $interpolationJSON = $this->serializer->serialize($interpolation, 'json');

        $fs = new Filesystem();
        if (!$fs->exists($this->tmpFolder)) {
            $fs->mkdir($this->tmpFolder);
        }

        $inputFile = $this->tmpFolder.'/'.$this->tmpFileName;
        $fs->dumpFile($inputFile, $interpolationJSON);

        $scriptName="interpolationCalculation.py";

        /** @var Process $process */
        $process = $this->pythonProcess
            ->setArguments(array('-W', 'ignore', $scriptName, $inputFile))
            ->setWorkingDirectory($this->kernel->getRootDir().'/../py/pyprocessing/interpolation')
            ->getProcess()
        ;
        
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $jsonResponse = $process->getOutput();
        $response = json_decode($jsonResponse);
        $this->data = $response->raster;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

}