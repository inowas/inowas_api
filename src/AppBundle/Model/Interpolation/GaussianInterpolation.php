<?php

namespace AppBundle\Model\Interpolation;

use Doctrine\Common\Collections\ArrayCollection;

class GaussianInterpolation
{

    protected $type = 'gaussian';

    /** @var  BoundingBox */
    protected $boundingBox;

    /** @var  GridSize $gridSize */
    protected $gridSize;

    /** @var  ArrayCollection */
    protected $pointValues;

    /**
     * KrigingInterpolation constructor.
     * @param GridSize|null $gridSize
     * @param BoundingBox|null $boundingBox
     * @param null $pointValues
     */
    public function __construct(GridSize $gridSize = null, BoundingBox $boundingBox = null, $pointValues = null)
    {
        $this->boundingBox = $boundingBox;
        $this->gridSize = $gridSize;
        $this->pointValues = $pointValues;

        if (is_null($pointValues)) {
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