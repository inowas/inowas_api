<?php

namespace Inowas\PyprocessingBundle\Model\Interpolation;

use AppBundle\Model\BoundingBox;
use AppBundle\Model\GridSize;
use Doctrine\Common\Collections\ArrayCollection;

class InterpolationParameter implements \JsonSerializable
{

    /**
     * @var string
     */
    protected $type;

    /**
     * @var  BoundingBox
     */
    protected $boundingBox;

    /**
     * @var GridSize $gridSize
     */
    protected $gridSize;

    /**
     * @var  ArrayCollection
     */
    protected $pointValues;

    /**
     * InterpolationParameter constructor.
     * @param $type
     * @param InterpolationConfiguration $interpolationParameter
     */
    public function __construct($type, InterpolationConfiguration $interpolationParameter)
    {
        $this->type = $type;
        $this->boundingBox = $interpolationParameter->getBoundingBox();
        $this->gridSize = $interpolationParameter->getGridSize();
        $this->pointValues = $interpolationParameter->getPointValues();
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return BoundingBox|null
     */
    public function getBoundingBox()
    {
        return $this->boundingBox;
    }

    /**
     * @return GridSize
     */
    public function getGridSize()
    {
        return $this->gridSize;
    }

    /**
     * @return ArrayCollection
     */
    public function getPointValues()
    {
        return $this->pointValues;
    }

    /**
     * @return mixed
     */
    function jsonSerialize()
    {
        return array(
            'type' => $this->type,
            'grid_size' => $this->gridSize,
            'bounding_box' => $this->boundingBox,
            'point_values' => $this->pointValues,
        );
    }
}
