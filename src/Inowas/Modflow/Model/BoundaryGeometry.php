<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model;

use CrEOF\Spatial\PHP\Types\AbstractGeometry;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;

class BoundaryGeometry
{
    /** @var AbstractGeometry */
    private $geometry;

    public static function fromPolygon(Polygon $polygon): BoundaryGeometry
    {
        $self = new self();
        $self->geometry = $polygon;
        return $self;
    }

    public static function fromLineString(LineString $lineString): BoundaryGeometry
    {
        $self = new self();
        $self->geometry = $lineString;
        return $self;
    }

    public static function fromPoint(Point $point): BoundaryGeometry
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
