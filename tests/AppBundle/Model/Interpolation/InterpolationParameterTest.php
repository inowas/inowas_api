<?php

namespace Tests\AppBundle\Model\Interpolation;

use AppBundle\Model\Interpolation\BoundingBox;
use AppBundle\Model\Interpolation\GridSize;
use AppBundle\Model\Interpolation\InterpolationParameter;
use AppBundle\Model\Interpolation\PointValue;
use AppBundle\Model\Point;
use AppBundle\Process\Interpolation\InterpolationConfiguration;
use AppBundle\Service\Interpolation;

class InterpolationParameterTest extends \PHPUnit_Framework_TestCase
{

    /** @var  InterpolationParameter */
    protected $interpolationParameter;

    public function setUp()
    {
        $interpolationConfiguration = new InterpolationConfiguration(
            new GridSize(3,4),
            new BoundingBox(12, 12, 13, 13),
            array(new PointValue(new Point(1,2,4321), 2)),
            array(Interpolation::TYPE_GAUSSIAN)
        );

        $this->interpolationParameter = new InterpolationParameter(Interpolation::TYPE_GAUSSIAN, $interpolationConfiguration);
    }

    public function testInstantiate(){
        $this->assertInstanceOf('AppBundle\Model\Interpolation\InterpolationParameter', $this->interpolationParameter);
        $this->assertEquals(Interpolation::TYPE_GAUSSIAN, $this->interpolationParameter->getType());
        $this->assertTrue(is_array($this->interpolationParameter->getPointValues()));
        $this->assertEquals(new GridSize(3, 4), $this->interpolationParameter->getGridSize());
        $this->assertEquals(new BoundingBox(12, 12, 13, 13), $this->interpolationParameter->getBoundingBox());
        $this->assertEquals(array(new PointValue(new Point(1,2,4321), 2)), $this->interpolationParameter->getPointValues());
    }

    public function testSerialize(){
        $expected = '{"type":"gaussian","grid_size":{"n_x":3,"n_y":4},"bounding_box":{"x_min":12,"x_max":12,"y_min":13,"y_max":13,"srid":0},"point_values":[{"x":1,"y":2,"value":2}]}';
        $this->assertEquals(json_decode($expected), json_decode(json_encode($this->interpolationParameter)));
    }
}
