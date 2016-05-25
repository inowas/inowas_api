<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Model\Interpolation\BoundingBox;
use AppBundle\Model\Interpolation\GridSize;
use AppBundle\Model\Interpolation\KrigingInterpolation;
use AppBundle\Model\Interpolation\PointValue;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class KrigingInterpolationTest extends \PHPUnit_Framework_TestCase
{

    /** @var  Serializer $serializer */
    protected $serializer;

    public function setUp()
    {
        $this->serializer = SerializerBuilder::create()->build();
    }

    public function testKrigingObjectHasGrid()
    {
        $krigingInterpolation = new KrigingInterpolation(new GridSize(12,13), null);
        $this->assertObjectHasAttribute('gridSize', $krigingInterpolation);
    }

    public function testKrigingObjectHasBoundingBox()
    {
        $krigingInterpolation = new KrigingInterpolation(new GridSize(12,13), new BoundingBox(1,2,3,4));
        $this->assertObjectHasAttribute('boundingBox', $krigingInterpolation);
    }

    public function testKrigingObjectCanAddPoints()
    {
        $krigingInterpolation = new KrigingInterpolation(new GridSize(12,13), null);
        $this->assertCount(0, $krigingInterpolation->getPointValues());
        $krigingInterpolation->addPoint(new PointValue(1,2,3));
        $this->assertCount(1, $krigingInterpolation->getPointValues());
        $this->assertEquals(new PointValue(1,2,3), $krigingInterpolation->getPointValues()->first());
    }

    public function testKrigingObjectCanRemovePoints()
    {
        $krigingInterpolation = new KrigingInterpolation(new GridSize(12,13), null);
        $point = new PointValue(1,2,3);
        $krigingInterpolation->addPoint($point);
        $this->assertCount(1, $krigingInterpolation->getPointValues());
        $krigingInterpolation->removePoint($point);
        $this->assertCount(0, $krigingInterpolation->getPointValues());
    }

    public function testSerializeObject()
    {
        $ki = new KrigingInterpolation(new GridSize(12,13), new BoundingBox(1,2,3,4));
        $ki->addPoint(new PointValue(1.1, 2.1, 3.1));
        $ski = $this->serializer->serialize($ki, 'json');

        $uki = json_decode($ski);

        $this->assertObjectHasAttribute('type', $uki);
        $this->assertEquals('kriging', $uki->type);

        $this->assertObjectHasAttribute('bounding_box', $uki);
        $this->assertObjectHasAttribute('x_min', $uki->bounding_box);
        $this->assertObjectHasAttribute('x_max', $uki->bounding_box);
        $this->assertObjectHasAttribute('y_min', $uki->bounding_box);
        $this->assertObjectHasAttribute('y_max', $uki->bounding_box);
        $this->assertEquals($ki->getBoundingBox()->getXMin(), $uki->bounding_box->x_min);
        $this->assertEquals($ki->getBoundingBox()->getXMax(), $uki->bounding_box->x_max);
        $this->assertEquals($ki->getBoundingBox()->getYMin(), $uki->bounding_box->y_min);
        $this->assertEquals($ki->getBoundingBox()->getYMax(), $uki->bounding_box->y_max);

        $this->assertObjectHasAttribute('grid_size', $uki);
        $this->assertObjectHasAttribute('n_x', $uki->grid_size);
        $this->assertObjectHasAttribute('n_y', $uki->grid_size);
        $this->assertEquals($ki->getGridSize()->getNX(), $uki->grid_size->n_x);
        $this->assertEquals($ki->getGridSize()->getNY(), $uki->grid_size->n_y);

        $this->assertObjectHasAttribute('point_values', $uki);
        $this->assertCount(1, $uki->point_values);
        $this->assertObjectHasAttribute('x', $uki->point_values[0]);
        $this->assertObjectHasAttribute('y', $uki->point_values[0]);
        $this->assertObjectHasAttribute('value', $uki->point_values[0]);

        $this->assertEquals($ki->getPointValues()->first()->getX(), $uki->point_values[0]->x);
        $this->assertEquals($ki->getPointValues()->first()->getY(), $uki->point_values[0]->y);
        $this->assertEquals($ki->getPointValues()->first()->getValue(), $uki->point_values[0]->value);
    }
}
