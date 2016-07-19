<?php

namespace Inowas\PyprocessingBundle\Model\Interpolation;

use AppBundle\Model\BoundingBox;
use AppBundle\Model\GridSize;
use Inowas\PyprocessingBundle\Exception\InvalidArgumentException;

class InterpolationConfiguration
{

    /** @var  GridSize */
    protected $gridSize;

    /** @var  BoundingBox */
    protected $boundingBox;

    /** @var  array */
    protected $pointValues;

    /** @var array $algorithms */
    protected $algorithms;

    /** @var  string */
    protected $currentAlgorithm;

    public function __construct(GridSize $gridSize, BoundingBox $boundingBox, array $pointValues, array $algorithms)
    {
        $this->gridSize = $gridSize;
        $this->boundingBox = $boundingBox;

        if (count($pointValues) == 0) {
            throw new InvalidArgumentException(sprintf('The parameter $pointValues has to contain at least one value, %s given', count($pointValues)));
        }

        $this->pointValues = $pointValues;

        if (count($algorithms) == 0) {
            throw new InvalidArgumentException(sprintf('The parameter $algorithms has to contain at least one value, %s given', count($algorithms)));
        }
        
        $this->algorithms = $algorithms;
    }

    /**
     * @return GridSize
     */
    public function getGridSize()
    {
        return $this->gridSize;
    }

    /**
     * @return BoundingBox
     */
    public function getBoundingBox()
    {
        return $this->boundingBox;
    }

    /**
     * @return array
     */
    public function getPointValues()
    {
        return $this->pointValues;
    }

    /**
     * @return array
     */
    public function getAlgorithms()
    {
        return $this->algorithms;
    }
}