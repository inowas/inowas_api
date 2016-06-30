<?php

namespace AppBundle\Model\Interpolation;

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
     * @param GridSize $gridSize
     * @param BoundingBox $boundingBox
     * @param ArrayCollection|null $pointValues
     */
    public function __construct(GridSize $gridSize, BoundingBox $boundingBox, ArrayCollection $pointValues = null)
    {
        $this->boundingBox = $boundingBox;
        $this->gridSize = $gridSize;
        $this->pointValues = $pointValues;

        if (is_null($pointValues)){
            $this->pointValues = new ArrayCollection();
        }
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
     * @param PointValue $pointValue
     */
    public function addPoint(PointValue $pointValue)
    {
        if (!$this->pointValues->contains($pointValue))
        {
            $this->pointValues->add($pointValue);
        }
    }

    /**
     * @param PointValue $pointValue
     */
    public function removePoint(PointValue $pointValue)
    {
        if ($this->pointValues->contains($pointValue))
        {
            $this->pointValues->removeElement($pointValue);
        }
    }

    /**
     * @return ArrayCollection
     */
    public function getPointValues()
    {
        return $this->pointValues;
    }
}