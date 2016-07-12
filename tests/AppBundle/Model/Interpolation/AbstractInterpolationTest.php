<?php

namespace Tests\AppBundle\Model\Interpolation;

use AppBundle\Model\Interpolation\AbstractInterpolation;
use AppBundle\Model\Interpolation\BoundingBox;
use AppBundle\Model\Interpolation\GridSize;
use AppBundle\Model\Interpolation\MeanInterpolation;
use AppBundle\Model\Interpolation\PointValue;
use AppBundle\Model\Point;
use AppBundle\Process\Interpolation\InterpolationParameter;
use AppBundle\Service\Interpolation;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class AbstractInterpolationTest extends \PHPUnit_Framework_TestCase
{

    /** @var  Serializer $serializer */
    protected $serializer;

    /** @var  AbstractInterpolation $interpolation */
    protected $interpolation;

    public function setUp()
    {
        $interpolationParameter = new InterpolationParameter(
            new GridSize(3,4),
            new BoundingBox(12, 12, 13, 13),
            array(new PointValue(new Point(1,2,4321), 2)),
            array(Interpolation::TYPE_GAUSSIAN)
        );

        $this->serializer = SerializerBuilder::create()->build();
        $this->interpolation = new MeanInterpolation($interpolationParameter);
    }

    public function testInstantiate(){
        $this->assertInstanceOf('AppBundle\Model\Interpolation\AbstractInterpolation', $this->interpolation);
        $this->assertTrue(is_array($this->interpolation->getPointValues()));
        $this->assertEquals(new GridSize(3, 4), $this->interpolation->getGridSize());
        $this->assertEquals(new BoundingBox(12, 12, 13, 13), $this->interpolation->getBoundingBox());
        $this->assertEquals(array(new PointValue(new Point(1,2,4321), 2)), $this->interpolation->getPointValues());
    }
}
