<?php

namespace AppBundle\Model;

use JMS\Serializer\Annotation as JMS;

class BoundingBox implements \JsonSerializable
{
    /** @JMS\Groups({"interpolation", "rasterdetails", "modeldetails", "geoimage", "modelProperties"}) */
    protected $xMin;

    /** @JMS\Groups({"interpolation", "rasterdetails", "modeldetails", "geoimage", "modelProperties"}) */
    protected $xMax;

    /** @JMS\Groups({"interpolation", "rasterdetails", "modeldetails", "geoimage", "modelProperties"}) */
    protected $yMin;

    /** @JMS\Groups({"interpolation", "rasterdetails", "modeldetails", "geoimage", "modelProperties"}) */
    protected $yMax;

    /** @JMS\Groups({"interpolation", "rasterdetails", "modeldetails", "geoimage", "modelProperties"}) */
    protected $srid;

    /**
     * BoundingBox constructor.
     * @param int $xMin
     * @param int $xMax
     * @param int $yMin
     * @param int $yMax
     * @param int $srid
     */
    public function __construct($xMin = 0, $xMax = 0, $yMin = 0, $yMax = 0, $srid = 0)
    {
        $this->xMin = $xMin;
        $this->xMax = $xMax;
        $this->yMin = $yMin;
        $this->yMax = $yMax;
        $this->srid = $srid;
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

    /**
     * @return mixed
     */
    public function getSrid()
    {
        return $this->srid;
    }

    /**
     * @param mixed $srid
     * @return BoundingBox
     */
    public function setSrid($srid)
    {
        $this->srid = $srid;
        return $this;
    }

    /**
     * @return mixed
     */
    function jsonSerialize()
    {
        return array(
            'x_min' => $this->xMin,
            'x_max' => $this->xMax,
            'y_min' => $this->yMin,
            'y_max' => $this->yMax,
            'srid' => $this->srid
        );
    }


}