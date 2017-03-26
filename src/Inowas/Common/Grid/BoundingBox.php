<?php

declare(strict_types=1);

namespace Inowas\Common\Grid;

class BoundingBox implements \JsonSerializable
{
    /** @var float */
    private $xMin;

    /** @var float */
    private $xMax;

    /** @var float */
    private $yMin;

    /** @var float */
    private $yMax;

    /** @var float */
    private $srid;

    public static function fromEPSG4326Coordinates($x1, $x2, $y1, $y2): BoundingBox
    {
        return new self($x1, $x2, $y1, $y2, 4326);
    }

    public static function fromCoordinates($x1, $x2, $y1, $y2, $srid): BoundingBox
    {
        return new self($x1, $x2, $y1, $y2, $srid);
    }

    public static function fromArray(array $bb): BoundingBox
    {
        return new self($bb['x_min'], $bb['x_max'], $bb['y_min'], $bb['y_max'], $bb['srid']);
    }

    private function __construct($x1, $x2, $y1, $y2, $srid)
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
    }

    public function xMin(): float
    {
        return $this->xMin;
    }

    public function xMax(): float
    {
        return $this->xMax;
    }

    public function yMin(): float
    {
        return $this->yMin;
    }

    public function yMax(): float
    {
        return $this->yMax;
    }

    public function srid(): float
    {
        return $this->srid;
    }

    public function toArray()
    {
        return array(
            'x_min' => $this->xMin,
            'x_max' => $this->xMax,
            'y_min' => $this->yMin,
            'y_max' => $this->yMax,
            'srid' => $this->srid
        );
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function toGeoJson(){
        return sprintf('{"type":"Polygon", "coordinates":[[[%f,%f],[%f,%f],[%f,%f],[%f,%f],[%f,%f]]]}',
            $this->xMin, $this->yMin,
            $this->xMin, $this->yMax,
            $this->xMax, $this->yMax,
            $this->xMax, $this->yMin,
            $this->xMin, $this->yMin
        );
    }
}

