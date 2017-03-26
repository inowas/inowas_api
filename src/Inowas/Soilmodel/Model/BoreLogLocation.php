<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model;

use CrEOF\Spatial\PHP\Types\Geometry\Point;

class BoreLogLocation
{
    /** @var  Point */
    private $point;

    public static function fromPoint(Point $point): BoreLogLocation
    {
        return new self($point);
    }

    private function __construct(Point $point)
    {
        $this->point = $point;
    }

    public function toArray():array
    {
        return $this->point->toArray();
    }

    public function toPoint(): Point
    {
        return $this->point;
    }

    public static function fromArray(array $data): BoreLogLocation
    {
        // Expecting array[x,y] for point-data
        $point = new Point($data[0], $data[1]);
        $self = new self($point);
        return $self;
    }
}
