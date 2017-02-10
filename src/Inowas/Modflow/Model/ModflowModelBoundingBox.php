<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model;

class ModflowModelBoundingBox
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

    public static function fromEPSG4326Coordinates($x1, $x2, $y1, $y2): ModflowModelBoundingBox
    {
        return new self($x1, $x2, $y1, $y2, 4326);
    }

    public static function fromCoordinates($x1, $x2, $y1, $y2, $srid): ModflowModelBoundingBox
    {
        return new self($x1, $x2, $y1, $y2, $srid);
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
}
