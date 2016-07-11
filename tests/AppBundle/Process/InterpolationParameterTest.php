<?php

namespace Tests\AppBundle\Process;

use AppBundle\Model\Interpolation\BoundingBox;
use AppBundle\Model\Interpolation\GridSize;
use AppBundle\Model\Interpolation\PointValue;
use AppBundle\Model\Point;
use AppBundle\Process\InterpolationParameter;
use AppBundle\Service\Interpolation;

class InterpolationParameterTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiate(){
        $interpolationParameter = new InterpolationParameter(
            new GridSize(1,2),
            new BoundingBox(1,2,3,4, 4326),
            array(new PointValue(new Point(1,2,4321), 2)),
            array(Interpolation::TYPE_GAUSSIAN)
        );
        $this->assertInstanceOf('AppBundle\Process\InterpolationParameter', $interpolationParameter);
        $this->assertInstanceOf('AppBundle\Model\Interpolation\GridSize', $interpolationParameter->getGridSize());
        $this->assertEquals(new GridSize(1,2), $interpolationParameter->getGridSize());
        $this->assertInstanceOf('AppBundle\Model\Interpolation\BoundingBox', $interpolationParameter->getBoundingBox());
        $this->assertEquals(new BoundingBox(1,2,3,4, 4326), $interpolationParameter->getBoundingBox());
        $this->assertEquals(array(new PointValue(new Point(1,2,4321), 2)), $interpolationParameter->getPointValues());
        $this->assertEquals(array(Interpolation::TYPE_GAUSSIAN), $interpolationParameter->getAlgorithms());
    }

    public function testThrowsExceptionInNoPointValueIsSet(){
        $this->setExpectedException('AppBundle\Exception\InvalidArgumentException');
        new InterpolationParameter(
            new GridSize(1,2),
            new BoundingBox(1,2,3,4, 4326),
            array(),
            array(Interpolation::TYPE_GAUSSIAN)
        );
    }

    public function testThrowsExceptionInNoAlgorithmIsSet(){
        $this->setExpectedException('AppBundle\Exception\InvalidArgumentException');
        new InterpolationParameter(
            new GridSize(1,2),
            new BoundingBox(1,2,3,4, 4326),
            array(new PointValue(new Point(1,2,4321), 2)),
            array()
        );
    }
}
