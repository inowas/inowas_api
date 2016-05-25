<?php

namespace AppBundle\Model\Interpolation;

use Doctrine\Common\Collections\ArrayCollection;

class KrigingInterpolation
{

    protected $type = 'kriging';

    /** @var  BoundingBox */
    protected $boundingBox;

    /** @var  GridSize $gridSize */
    protected $gridSize;

    /** @var  ArrayCollection */
    protected $pointValues;

    /**
     * KridgingInterpolation constructor.
     * @param GridSize|null $gridSize
     * @param BoundingBox|null $boundingBox
     */
    public function __construct(GridSize $gridSize = null, BoundingBox $boundingBox = null)
    {
        $this->boundingBox = $boundingBox;
        $this->gridSize = $gridSize;
        $this->pointValues = new ArrayCollection();
    }

    /**
     * @return BoundingBox|null
     */
    public function getBoundingBox()
    {
        return $this->boundingBox;
    }

    /**
     * @param mixed $boundingBox
     */
    public function setBoundingBox($boundingBox)
    {
        $this->boundingBox = $boundingBox;
    }

    /**
     * @return GridSize
     */
    public function getGridSize()
    {
        return $this->gridSize;
    }

    /**
     * @param GridSize $gridSize
     * @return KrigingInterpolation
     */
    public function setGridSize($gridSize)
    {
        $this->gridSize = $gridSize;
        return $this;
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