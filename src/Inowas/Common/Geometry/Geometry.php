<?php

declare(strict_types=1);

namespace Inowas\Common\Geometry;

class Geometry
{
    /** @var AbstractGeometry */
    private $geometry;

    public static function fromPolygon(Polygon $polygon): Geometry
    {
        $self = new self();
        $self->geometry = $polygon;
        return $self;
    }

    public static function fromLineString(LineString $lineString): Geometry
    {
        $self = new self();
        $self->geometry = $lineString;
        return $self;
    }

    public static function fromPoint(Point $point): Geometry
    {
        $self = new self();
        $self->geometry = $point;
        return $self;
    }

    public function toArray()
    {
        return $this->geometry->toArray();
    }

    public function toJson()
    {
        return $this->geometry->toJson();
    }
}
