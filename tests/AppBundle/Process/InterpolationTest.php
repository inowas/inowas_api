<?php

namespace Tests\AppBundle\Process;

use AppBundle\Model\Interpolation\BoundingBox;
use AppBundle\Model\Interpolation\GridSize;
use AppBundle\Model\Interpolation\PointValue;
use AppBundle\Model\Point;
use AppBundle\Process\Interpolation;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class InterpolationTest extends WebTestCase
{
    /** @var  Interpolation */
    protected $interpolation;

    public function setUp()
    {
        self::bootKernel();

        $httpKernel = static::$kernel->getContainer()
            ->get('kernel');

        $serializer = static::$kernel->getContainer()
            ->get('serializer');

        $this->interpolation = new Interpolation($serializer, $httpKernel);
    }
    
    public function testSetAndGetGridSize()
    {
        $gridSize = new GridSize(10, 11);
        $this->interpolation->setGridSize($gridSize);
        $this->assertEquals($gridSize, $this->interpolation->getGridSize());
    }

    public function testSetAndGetBoundingBox()
    {
        $boundingBox = new BoundingBox(-1.2, 2.1, -5.1, 1.5);
        $this->interpolation->setBoundingBox($boundingBox);
        $this->assertEquals($boundingBox, $this->interpolation->getBoundingBox());
    }

    public function testAddingPointValue(){
        $pointValue = new PointValue(new Point(1, 2, 4326), 3);
        $this->interpolation->addPointValue($pointValue);
        $this->assertCount(1, $this->interpolation->getPoints());
        $this->assertEquals($pointValue, $this->interpolation->getPoints()->first());
    }

    public function testAddingOnePointValueWillNotBeAddedTwice()
    {
        $pointValue = new PointValue(new Point(1, 2, 4326), 3);
        $this->interpolation->addPointValue($pointValue);
        $this->interpolation->addPointValue($pointValue);
        $this->assertCount(1, $this->interpolation->getPoints());
        $this->assertEquals($pointValue, $this->interpolation->getPoints()->first());
    }

    public function testThrowExceptionIfAlgorithmIsUnknown()
    {
        $this->interpolation->setGridSize(new GridSize(10,11));
        $this->interpolation->setBoundingBox(new BoundingBox(-10.1, 10.2, -5.1, 5.2));
        $this->interpolation->addPointValue(new PointValue(new Point(1, 2, 4326), 3));

        $unknownAlgorithm = 'foo';
        $this->setExpectedException(
            'AppBundle\Exception\InvalidArgumentException',
            'Algorithm '.$unknownAlgorithm.' not found.'
        );

        $this->interpolation->interpolate($unknownAlgorithm);
    }

    public function testThrowExceptionIfIfGridSizeIsNotSet()
    {
        $this->interpolation->setBoundingBox(new BoundingBox(-10.1, 10.2, -5.1, 5.2));
        $this->interpolation->addPointValue(new PointValue(new Point(1, 2, 4326), 3));

        $this->setExpectedException(
            'AppBundle\Exception\InvalidArgumentException',
            'GridSize not set.'
        );
        $this->interpolation->interpolate(Interpolation::TYPE_MEAN);
    }

    public function testThrowExceptionIfIfBoundingBoxIsNotSet()
    {
        $this->interpolation->setGridSize(new GridSize(10,11));
        $this->interpolation->addPointValue(new PointValue(new Point(1, 2, 4326), 3));

        $this->setExpectedException(
            'AppBundle\Exception\InvalidArgumentException',
            'BoundingBox not set.'
        );
        $this->interpolation->interpolate(Interpolation::TYPE_MEAN);
    }

    public function testThrowExceptionIfIfNoPointIstSet()
    {
        $this->interpolation->setGridSize(new GridSize(10,11));
        $this->interpolation->setBoundingBox(new BoundingBox(-10.1, 10.2, -5.1, 5.2));

        $this->setExpectedException(
            'AppBundle\Exception\InvalidArgumentException',
            'No PointValues set.'
        );
        $this->interpolation->interpolate(Interpolation::TYPE_MEAN);
    }

    public function testIdwInterpolation(){
        $this->interpolation->setGridSize(new GridSize(10,11));
        $this->interpolation->setBoundingBox(new BoundingBox(-10.1, 10.2, -5.1, 5.2));
        $this->interpolation->addPointValue(new PointValue(new Point(1, 2, 4326), 3));
        $this->interpolation->addPointValue(new PointValue(new Point(1, 2, 4326), 4));
        $result = $this->interpolation->interpolate(Interpolation::TYPE_IDW);
        $this->assertInstanceOf('AppBundle\Process\InterpolationResult', $result);
        $this->assertEquals(Interpolation::TYPE_IDW, $result->getAlgorithm());
    }

    public function testMeanInterpolation(){
        $this->interpolation->setGridSize(new GridSize(10,11));
        $this->interpolation->setBoundingBox(new BoundingBox(-10.1, 10.2, -5.1, 5.2));
        $this->interpolation->addPointValue(new PointValue(new Point(1, 2, 4326), 3));
        $this->interpolation->addPointValue(new PointValue(new Point(1, 2, 4326), 4));
        $result = $this->interpolation->interpolate(Interpolation::TYPE_MEAN);
        $this->assertInstanceOf('AppBundle\Process\InterpolationResult', $result);
        $this->assertEquals(Interpolation::TYPE_MEAN, $result->getAlgorithm());
    }

    public function testGaussianInterpolation(){
        $this->interpolation->setGridSize(new GridSize(10,11));
        $this->interpolation->setBoundingBox(new BoundingBox(0, 10, 0, 10));
        $this->interpolation->addPointValue(new PointValue(new Point(1, 5, 4326), 3));
        $this->interpolation->addPointValue(new PointValue(new Point(2, 8, 4326), 3));
        $this->interpolation->addPointValue(new PointValue(new Point(7, 2, 4326), 3));
        $this->interpolation->addPointValue(new PointValue(new Point(6, 4, 4326), 3));
        $this->interpolation->addPointValue(new PointValue(new Point(8, 2, 4326), 3));
        $this->interpolation->addPointValue(new PointValue(new Point(9, 9, 4326), 3));
        $result = $this->interpolation->interpolate(Interpolation::TYPE_GAUSSIAN);
        $this->assertInstanceOf('AppBundle\Process\InterpolationResult', $result);
        $this->assertEquals(Interpolation::TYPE_GAUSSIAN, $result->getAlgorithm());
    }

    public function testMultipleInterpolationAlgorithms(){
        $this->interpolation->setGridSize(new GridSize(10,11));
        $this->interpolation->setBoundingBox(new BoundingBox(0, 10, 0, 10));
        $this->interpolation->addPointValue(new PointValue(new Point(1, 5, 4326), 3));
        $this->interpolation->addPointValue(new PointValue(new Point(2, 8, 4326), 3));
        $this->interpolation->addPointValue(new PointValue(new Point(7, 2, 4326), 3));
        $this->interpolation->addPointValue(new PointValue(new Point(6, 4, 4326), 3));
        $this->interpolation->addPointValue(new PointValue(new Point(8, 2, 4326), 3));
        $this->interpolation->addPointValue(new PointValue(new Point(9, 9, 4326), 3));
        $result = $this->interpolation->interpolate(array(0 => Interpolation::TYPE_GAUSSIAN, 1 => Interpolation::TYPE_MEAN));
        $this->assertInstanceOf('AppBundle\Process\InterpolationResult', $result);
        $this->assertEquals(Interpolation::TYPE_GAUSSIAN, $result->getAlgorithm());
    }

    public function testInterpolationAlgorithmsFallback(){
        $this->interpolation->setGridSize(new GridSize(10,11));
        $this->interpolation->setBoundingBox(new BoundingBox(0, 10, 0, 10));
        $this->interpolation->addPointValue(new PointValue(new Point(1, 5), 3));
        $this->interpolation->addPointValue(new PointValue(new Point(2, 8), 3));
        $result = $this->interpolation->interpolate(array(0 => Interpolation::TYPE_GAUSSIAN, 1 => Interpolation::TYPE_MEAN));
        $this->assertInstanceOf('AppBundle\Process\InterpolationResult', $result);
        $this->assertEquals(Interpolation::TYPE_MEAN, $result->getAlgorithm());
    }
}