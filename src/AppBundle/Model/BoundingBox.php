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

    /** @var float */
    protected $dXInMeters = 0.0;

    /** @var float */
    protected $dYInMeters = 0.0;

    /**
     * BoundingBox constructor.
     * @param int $x1
     * @param int $x2
     * @param int $y1
     * @param int $y2
     * @param int $srid
     * @param float $dXInMeters
     * @param float $dYInMeters
     */
    public function __construct($x1 = 0, $x2 = 0, $y1 = 0, $y2 = 0, $srid = 0, $dXInMeters = 0.0, $dYInMeters = 0.0)
    {
        if ($x1 > $x2){
            $this->xMin = $x2;
            $this->xMax = $x1;
        } else {
            $this->xMin = $x1;
            $this->xMax = $x2;
        }

        if ($y1 > $y2){
            $this->yMin = $y2;
            $this->yMax = $y1;
        } else {
            $this->yMin = $y1;
            $this->yMax = $y2;
        }

        $this->srid = $srid;
        $this->dXInMeters = $dXInMeters;
        $this->dYInMeters = $dYInMeters;
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
     * @return float
     */
    public function getDXInMeters(): float
    {
        return $this->dXInMeters;
    }

    /**
     * @param float $dXInMeters
     * @return BoundingBox
     */
    public function setDXInMeters(float $dXInMeters): BoundingBox
    {
        $this->dXInMeters = $dXInMeters;
        return $this;
    }

    /**
     * @return float
     */
    public function getDYInMeters(): float
    {
        return $this->dYInMeters;
    }

    /**
     * @param float $dYInMeters
     * @return BoundingBox
     */
    public function setDYInMeters(float $dYInMeters): BoundingBox
    {
        $this->dYInMeters = $dYInMeters;
        return $this;
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
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
