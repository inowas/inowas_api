<?php

namespace InowasPyprocessingBundle\Model\Interpolation;

use AppBundle\Model\BoundingBox;
use AppBundle\Model\GridSize;

class InterpolationResult
{

    /** @var  string */
    protected $algorithm;

    /** @var  string */
    protected $data;

    /** @var  GridSize */
    protected $gridSize;

    /** @var  BoundingBox */
    protected $boundingBox;

    public function __construct($algorithm, array $data, GridSize $gridSize, BoundingBox $boundingBox)
    {
        $this->algorithm = $algorithm;
        $this->data = $data;
        $this->gridSize = $gridSize;
        $this->boundingBox = $boundingBox;
    }

    /**
     * @return string
     */
    public function getAlgorithm()
    {
        return $this->algorithm;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
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
}