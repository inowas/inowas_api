<?php

declare(strict_types=1);

namespace Inowas\Common\Grid;

use Inowas\Common\Geometry\Point;

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

    /**
     * DeltaX in meters
     * @var  float
     */
    private $dX;

    /**
     * DeltaY in meters
     * @var float
     */
    private $dY;


    public static function fromEPSG4326Coordinates($x1, $x2, $y1, $y2, $dXinMeters, $dYinMeters): BoundingBox
    {
        return new self($x1, $x2, $y1, $y2, 4326, $dXinMeters, $dYinMeters);
    }

    public static function fromCoordinates($x1, $x2, $y1, $y2, $srid, $dXinMeters = 0, $dYinMeters = 0): BoundingBox
    {
        return new self($x1, $x2, $y1, $y2, $srid, $dXinMeters, $dYinMeters);
    }

    public static function fromArray(array $bb): BoundingBox
    {
        return new self($bb['x_min'], $bb['x_max'], $bb['y_min'], $bb['y_max'], $bb['srid'], $bb['d_x'], $bb['d_y']);
    }

    private function __construct($x1, $x2, $y1, $y2, $srid, $dX, $dY)
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

        $this->dX = $dX;
        $this->dY = $dY;
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

    public function dX(): float
    {
        return $this->dX;
    }

    public function dY(): float
    {
        return $this->dY;
    }

    public function toArray()
    {
        return array(
            'x_min' => $this->xMin,
            'x_max' => $this->xMax,
            'y_min' => $this->yMin,
            'y_max' => $this->yMax,
            'srid' => $this->srid,
            'd_x' => $this->dX,
            'd_y' => $this->dY,
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

    public function upperLeft(): Point
    {
        return new Point($this->xMin, $this->yMax);
    }

    public function sameAs(BoundingBox $boundingBox): bool
    {
        return (
            ($this->xMin() === $boundingBox->xMin()) &&
            ($this->xMax() === $boundingBox->xMax()) &&
            ($this->yMin() === $boundingBox->yMin()) &&
            ($this->yMax() === $boundingBox->yMax()) &&
            ($this->srid() === $boundingBox->srid()) &&
            ($this->dX() === $boundingBox->dX()) &&
            ($this->dY() === $boundingBox->dY())
        );
    }
}
