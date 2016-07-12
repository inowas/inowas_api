<?php

namespace AppBundle\Model\Interpolation;

use AppBundle\Process\Interpolation\InterpolationParameter;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as JMS;


abstract class AbstractInterpolation
{
    /**
     * @var  BoundingBox
     *
     * @JMS\Groups({"interpolation"})
     */
    protected $boundingBox;

    /**
     * @var GridSize $gridSize
     *
     * @JMS\Groups({"interpolation"})
     */
    protected $gridSize;

    /**
     * @var  ArrayCollection
     *
     * @JMS\Groups({"interpolation"})
     */
    protected $pointValues;

    /**
     * AbstractInterpolation constructor.
     * @param InterpolationParameter $interpolationParameter
     */
    public function __construct(InterpolationParameter $interpolationParameter)
    {
        $this->boundingBox = $interpolationParameter->getBoundingBox();
        $this->gridSize = $interpolationParameter->getGridSize();
        $this->pointValues = $interpolationParameter->getPointValues();
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
}