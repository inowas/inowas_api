<?php

namespace Tests\AppBundle\Model\Interpolation;

use AppBundle\Model\Interpolation\BoundingBox;
use AppBundle\Model\Interpolation\GridSize;
use AppBundle\Model\Interpolation\KrigingInterpolation;
use AppBundle\Model\Interpolation\PointValue;
use AppBundle\Model\Point;
use AppBundle\Process\Interpolation\InterpolationParameter;
use AppBundle\Service\Interpolation;
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

    public function testKrigingObjectHasBoundingBox()
    {
        $interpolationParameter = new InterpolationParameter(
            new GridSize(12,13),
            new BoundingBox(1,2,3,4),
            array(new PointValue(new Point(1.1, 2.1, 4326), 3.1)),
            array(Interpolation::TYPE_GAUSSIAN)
        );

        $krigingInterpolation = new KrigingInterpolation($interpolationParameter);
        $this->assertObjectHasAttribute('boundingBox', $krigingInterpolation);
    }

    public function testSerializeObject()
    {

        $interpolationParameter = new InterpolationParameter(
            new GridSize(12,13),
            new BoundingBox(1,2,3,4),
            array(new PointValue(new Point(1.1, 2.1, 4326), 3.1)),
            array(Interpolation::TYPE_GAUSSIAN)
        );

        $krigingInterpolation = new KrigingInterpolation($interpolationParameter);
        $ski = $this->serializer->serialize($krigingInterpolation, 'json');
        $uki = json_decode($ski);

        $this->assertObjectHasAttribute('type', $uki);
        $this->assertEquals('kriging', $uki->type);

        $this->assertObjectHasAttribute('bounding_box', $uki);
        $this->assertObjectHasAttribute('x_min', $uki->bounding_box);
        $this->assertObjectHasAttribute('x_max', $uki->bounding_box);
        $this->assertObjectHasAttribute('y_min', $uki->bounding_box);
        $this->assertObjectHasAttribute('y_max', $uki->bounding_box);
        $this->assertEquals($krigingInterpolation->getBoundingBox()->getXMin(), $uki->bounding_box->x_min);
        $this->assertEquals($krigingInterpolation->getBoundingBox()->getXMax(), $uki->bounding_box->x_max);
        $this->assertEquals($krigingInterpolation->getBoundingBox()->getYMin(), $uki->bounding_box->y_min);
        $this->assertEquals($krigingInterpolation->getBoundingBox()->getYMax(), $uki->bounding_box->y_max);

        $this->assertObjectHasAttribute('grid_size', $uki);
        $this->assertObjectHasAttribute('n_x', $uki->grid_size);
        $this->assertObjectHasAttribute('n_y', $uki->grid_size);
        $this->assertEquals($krigingInterpolation->getGridSize()->getNX(), $uki->grid_size->n_x);
        $this->assertEquals($krigingInterpolation->getGridSize()->getNY(), $uki->grid_size->n_y);

        $this->assertObjectHasAttribute('point_values', $uki);
        $this->assertCount(1, $uki->point_values);
        $this->assertObjectHasAttribute('x', $uki->point_values[0]);
        $this->assertObjectHasAttribute('y', $uki->point_values[0]);
        $this->assertObjectHasAttribute('value', $uki->point_values[0]);

        $this->assertEquals($krigingInterpolation->getPointValues()[0]->getX(), $uki->point_values[0]->x);
        $this->assertEquals($krigingInterpolation->getPointValues()[0]->getY(), $uki->point_values[0]->y);
        $this->assertEquals($krigingInterpolation->getPointValues()[0]->getValue(), $uki->point_values[0]->value);
    }
}
