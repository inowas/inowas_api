<?php

namespace Inowas\PyprocessingBundle\Model\Interpolation;

use Inowas\PyprocessingBundle\Exception\InvalidArgumentException;

class InterpolationConfiguration
{

    protected $gridSize;

    protected $boundingBox;

    /** @var  array */
    protected $pointValues;

    /** @var array $algorithms */
    protected $algorithms;

    /** @var  string */
    protected $currentAlgorithm;

    public function __construct($gridSize, $boundingBox, array $pointValues, array $algorithms)
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
     * @return mixed
     */
    public function getGridSize()
    {
        return $this->gridSize;
    }

    /**
     * @return mixed
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
