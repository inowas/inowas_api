<?php

namespace AppBundle\Model\Interpolation;


class Result
{
    /** @var  GridSize */
    protected $gridSize;

    /** @var  BoundingBox */
    protected $boundingBox;

    /** @var array */
    protected $data;


    public function __construct(GridSize $gridSize = null, BoundingBox $boundingBox = null, $data = null)
    {
        $this->gridSize = $gridSize;
        $this->boundingBox = $boundingBox;
        $this->data = $data;
    }

    public function setGridSize(GridSize $gridSize)
    {
        $this->gridSize = $gridSize;
    }

    public function setBoundingBox(BoundingBox $boundingBox)
    {
        $this->boundingBox = $boundingBox;
    }

    public function setData($data)
    {
        if (!is_array($data)) {
            throw new \InvalidArgumentException('Data is not an array');
        }

        /* Todo: check all elements to numeric */
        $this->data = $data;
    }
}