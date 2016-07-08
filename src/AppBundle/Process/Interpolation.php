<?php

namespace AppBundle\Process;

use AppBundle\Exception\InvalidArgumentException;
use AppBundle\Model\Interpolation\BoundingBox;
use AppBundle\Model\Interpolation\GridSize;
use AppBundle\Model\Interpolation\PointValue;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

class Interpolation
{
    const TYPE_IDW = 'idw';
    const TYPE_MEAN = 'mean';
    const TYPE_GAUSSIAN = 'gaussian';

    /** @var array */
    private $availableTypes = [self::TYPE_MEAN, self::TYPE_GAUSSIAN, self::TYPE_IDW];

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
        $this->points = new ArrayCollection();
        $this->serializer = $serializer;
        $this->kernel = $kernel;
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


    public function addPointValue(PointValue $pointValue)
    {
        if (!$this->points->contains($pointValue)) {
            $this->points[] = $pointValue;
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
        if (is_array($algorithm)) {
            $algorithms = $algorithm;
        } else {
            $algorithms = array();
            $algorithms[] = $algorithm;
        }

        foreach ($algorithms as $algorithm) {
            if (!in_array($algorithm, $this->availableTypes)) {
                throw new InvalidArgumentException(sprintf('Algorithm %s not found.', $algorithm));
            }
        }

        if (!$this->gridSize instanceof GridSize) {
            throw new InvalidArgumentException('GridSize not set.');
        }

        if (!$this->boundingBox instanceof BoundingBox) {
            throw new InvalidArgumentException('BoundingBox not set.');
        }

        if ($this->points->count() == 0) {
            throw new InvalidArgumentException('No PointValues set.');
        }

        for ($i = 0; $i < count($algorithms); $i++) {

            $class = 'AppBundle\Model\Interpolation\\' . ucfirst($algorithms[$i]) . 'Interpolation';
            $interpolation = new $class($this->gridSize, $this->boundingBox, $this->points);

            $interpolationJSON = $this->serializer->serialize(
                $interpolation,
                'json',
                SerializationContext::create()->setGroups(array('interpolation'))
            );

            $randomFileName = Uuid::uuid4()->toString();
            $inputFileName = $this->kernel->getContainer()->getParameter('inowas.temp_folder') . '/' . $randomFileName . '.in';
            $outputFileName = $this->kernel->getContainer()->getParameter('inowas.temp_folder') . '/' . $randomFileName . '.out';

            $fs = new Filesystem();
            $fs->dumpFile($inputFileName, $interpolationJSON);
            $fs->touch($outputFileName);

            $configuration = new InterpolationProcessConfiguration(
                ProcessFile::fromFilename($inputFileName),
                ProcessFile::fromFilename($outputFileName)
            );

            $configuration->setWorkingDirectory($this->kernel->getContainer()->getParameter('inowas.interpolation.working_directory'));
            $process = new InterpolationProcess($configuration);

            if ($process->interpolate())
            {
                $jsonResults = file_get_contents($outputFileName);
                $results = json_decode($jsonResults);
                
                return new InterpolationResult($results->method, $results->raster);
                break;
            }
        }

        return false;
    }
}
