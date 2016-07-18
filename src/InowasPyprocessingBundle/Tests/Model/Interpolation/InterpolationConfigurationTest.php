<?php

namespace InowasPyprocessingBundle\Tests\Model\Interpolation;

use AppBundle\Model\BoundingBox;
use AppBundle\Model\GridSize;
use AppBundle\Model\Point;
use AppBundle\Model\PointValue;
use InowasPyprocessingBundle\Exception\InvalidArgumentException;
use InowasPyprocessingBundle\Model\Interpolation\InterpolationConfiguration;
use InowasPyprocessingBundle\Service\Interpolation;

class InterpolationConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /** @var  BoundingBox */
    protected $boundingBox;

    /** @var  GridSize */
    protected $gridSize;

    /** @var  array */
    protected $pointValues;

    /** @var  array */
    protected $algorithms;

    public function setUp()
    {
        $this->boundingBox = new BoundingBox(1,2,3,4, 4326);
        $this->gridSize = new GridSize(1,2);
        $this->pointValues = array(new PointValue(new Point(1,2,4321), 2));
        $this->algorithms =  array(Interpolation::TYPE_GAUSSIAN);
    }

    public function testInstantiate(){
        $interpolationParameter = new InterpolationConfiguration(
            $this->gridSize,
            $this->boundingBox,
            $this->pointValues,
            $this->algorithms
        );
        $this->assertInstanceOf(InterpolationConfiguration::class, $interpolationParameter);
        $this->assertInstanceOf(GridSize::class, $interpolationParameter->getGridSize());
        $this->assertEquals($this->gridSize, $interpolationParameter->getGridSize());
        $this->assertInstanceOf(BoundingBox::class, $interpolationParameter->getBoundingBox());
        $this->assertEquals($this->boundingBox, $interpolationParameter->getBoundingBox());
        $this->assertEquals($this->pointValues, $interpolationParameter->getPointValues());
        $this->assertEquals($this->algorithms, $interpolationParameter->getAlgorithms());
    }

    public function testThrowsExceptionInNoPointValueIsSet(){
        $this->setExpectedException(InvalidArgumentException::class);
        new InterpolationConfiguration(
            $this->gridSize,
            $this->boundingBox,
            array(),
            $this->algorithms
        );
    }

    public function testThrowsExceptionInNoAlgorithmIsSet(){
        $this->setExpectedException(InvalidArgumentException::class);
        new InterpolationConfiguration(
            $this->gridSize,
            $this->boundingBox,
            $this->pointValues,
            array()
        );
    }
}
