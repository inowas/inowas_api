<?php

declare(strict_types=1);

namespace Inowas\Common\Grid;

use Inowas\Common\Geometry\Point;

class UpperLeftCoordinates
{
    /** @var  float */
    private $xul;

    /** @var  float */
    private $yul;


    public static function fromPoint(Point $point): UpperLeftCoordinates
    {
        $self = new self();
        $self->xul = $point->getX();
        $self->yul = $point->getY();
        return $self;
    }

    public static function none(): UpperLeftCoordinates
    {
        return new self();
    }

    private function __construct(){}

    public function xul(): ?float
    {
        return $this->xul;
    }

    public function yul(): ?float
    {
        return $this->yul;
    }
}
