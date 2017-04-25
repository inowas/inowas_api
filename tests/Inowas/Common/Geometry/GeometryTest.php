<?php

declare(strict_types=1);

namespace Tests\Inowas\Common\Geometry;

use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Geometry\Point;
use Inowas\Common\Geometry\Srid;

class GeometryTest extends \PHPUnit_Framework_TestCase
{
    public function test_create_geometry_with_srid(): void
    {
        $point = Geometry::fromPoint(new Point(10, 10, 4326));
        $this->assertInstanceOf(Srid::class, $point->srid());
        $this->assertEquals(4326, $point->srid()->toInteger());
    }
}
