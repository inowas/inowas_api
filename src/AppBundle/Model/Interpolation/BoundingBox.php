<?php

namespace AppBundle\Model\Interpolation;

use JMS\Serializer\Annotation as JMS;

class BoundingBox
{
    /** @JMS\Groups({"interpolation"}) */
    protected $xMin;

    /** @JMS\Groups({"interpolation"}) */
    protected $xMax;

    /** @JMS\Groups({"interpolation"}) */
    protected $yMin;

    /** @JMS\Groups({"interpolation"}) */
    protected $yMax;

    /**
     * BoundingBox constructor.
     * @param int $xMin
     * @param int $xMax
     * @param int $yMin
     * @param int $yMax
     */
    public function __construct($xMin = 0, $xMax = 0, $yMin = 0, $yMax = 0)
    {
        $this->xMin = $xMin;
        $this->xMax = $xMax;
        $this->yMin = $yMin;
        $this->yMax = $yMax;
    }

    /**
     * @return mixed
     */
    public function getXMin()
    {
        return $this->xMin;
    }

    /**
     * @param mixed $xMin
     * @return BoundingBox
     */
    public function setXMin($xMin)
    {
        $this->xMin = $xMin;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getXMax()
    {
        return $this->xMax;
    }

    /**
     * @param mixed $xMax
     * @return BoundingBox
     */
    public function setXMax($xMax)
    {
        $this->xMax = $xMax;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getYMin()
    {
        return $this->yMin;
    }

    /**
     * @param mixed $yMin
     * @return BoundingBox
     */
    public function setYMin($yMin)
    {
        $this->yMin = $yMin;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getYMax()
    {
        return $this->yMax;
    }

    /**
     * @param mixed $yMax
     * @return BoundingBox
     */
    public function setYMax($yMax)
    {
        $this->yMax = $yMax;
        return $this;
    }
}