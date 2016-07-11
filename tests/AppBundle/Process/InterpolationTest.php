<?php

namespace Tests\AppBundle\Process;

use AppBundle\Model\Interpolation\BoundingBox;
use AppBundle\Model\Interpolation\GridSize;
use AppBundle\Model\Interpolation\PointValue;
use AppBundle\Model\Point;
use AppBundle\Service\Interpolation;
use AppBundle\Process\InterpolationParameter;
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

        $configFileCreator = static::$kernel->getContainer()
            ->get('inowas.process.configurationfilecreatorfactory');

        $this->interpolation = new Interpolation($httpKernel, $configFileCreator);
    }

    public function testThrowExceptionIfAlgorithmIsUnknown()
    {
        $interpolationParameter = new InterpolationParameter(
            new GridSize(10,11),
            new BoundingBox(-10.1, 10.2, -5.1, 5.2),
            array(
                new PointValue(new Point(1, 2, 4326), 3)
            ),
            array('foo')
        );

        $unknownAlgorithm = 'foo';
        $this->setExpectedException(
            'AppBundle\Exception\InvalidArgumentException',
            'Algorithm '.$unknownAlgorithm.' not found.'
        );

        $this->interpolation->interpolate($interpolationParameter);
    }

    public function testThrowExceptionIfIfNoPointIstSet()
    {
        $this->setExpectedException('AppBundle\Exception\InvalidArgumentException');
        new InterpolationParameter(
            new GridSize(10,11),
            new BoundingBox(-10.1, 10.2, -5.1, 5.2),
            array(),
            array(Interpolation::TYPE_IDW)
        );
    }

    public function testIdwInterpolation(){
        $interpolationConfiguration = new InterpolationParameter(
            new GridSize(10,11),
            new BoundingBox(-10.1, 10.2, -5.1, 5.2),
            array(
                new PointValue(new Point(1, 2, 4326), 3),
                new PointValue(new Point(1, 3, 4326), 4)
            ),
            array(Interpolation::TYPE_IDW)
        );

        $result = $this->interpolation->interpolate($interpolationConfiguration);
        $this->assertInstanceOf('AppBundle\Process\InterpolationResult', $result);
        $this->assertEquals(Interpolation::TYPE_IDW, $result->getAlgorithm());
    }

    public function testMeanInterpolation(){
        $interpolationConfiguration = new InterpolationParameter(
            new GridSize(10,11),
            new BoundingBox(-10.1, 10.2, -5.1, 5.2),
            array(
                new PointValue(new Point(1, 2, 4326), 3),
                new PointValue(new Point(1, 3, 4326), 4)
            ),
            array(Interpolation::TYPE_MEAN)
        );

        $result = $this->interpolation->interpolate($interpolationConfiguration);
        $this->assertInstanceOf('AppBundle\Process\InterpolationResult', $result);
        $this->assertEquals(Interpolation::TYPE_MEAN, $result->getAlgorithm());
    }

    public function testGaussianInterpolation(){
        $interpolationConfiguration = new InterpolationParameter(
            new GridSize(10,11),
            new BoundingBox(0, 10, 0, 10),
            array(
                new PointValue(new Point(1, 5, 4326), 3),
                new PointValue(new Point(2, 8, 4326), 3),
                new PointValue(new Point(7, 2, 4326), 3),
                new PointValue(new Point(6, 4, 4326), 3),
                new PointValue(new Point(8, 2, 4326), 3),
                new PointValue(new Point(9, 9, 4326), 3)
            ),
            array(Interpolation::TYPE_GAUSSIAN)
        );

        $result = $this->interpolation->interpolate($interpolationConfiguration);
        $this->assertInstanceOf('AppBundle\Process\InterpolationResult', $result);
        $this->assertEquals(Interpolation::TYPE_GAUSSIAN, $result->getAlgorithm());
    }

    public function testMultipleInterpolationAlgorithms(){
        $interpolationConfiguration = new InterpolationParameter(
            new GridSize(10,11),
            new BoundingBox(0, 10, 0, 10),
            array(
                new PointValue(new Point(1, 5, 4326), 3),
                new PointValue(new Point(2, 8, 4326), 3),
                new PointValue(new Point(7, 2, 4326), 3),
                new PointValue(new Point(6, 4, 4326), 3),
                new PointValue(new Point(8, 2, 4326), 3),
                new PointValue(new Point(9, 9, 4326), 3)
            ),
            array(Interpolation::TYPE_GAUSSIAN, Interpolation::TYPE_MEAN)
        );

        $result = $this->interpolation->interpolate($interpolationConfiguration);
        $this->assertInstanceOf('AppBundle\Process\InterpolationResult', $result);
        $this->assertEquals(Interpolation::TYPE_GAUSSIAN, $result->getAlgorithm());
    }

    public function testInterpolationAlgorithmsFallback()
    {
        $interpolationConfiguration = new InterpolationParameter(
            new GridSize(10, 11),
            new BoundingBox(-10.1, 10.2, -5.1, 5.2),
            array(
                new PointValue(new Point(1, 2, 4326), 3),
                new PointValue(new Point(1, 3, 4326), 4)
            ),
            array(Interpolation::TYPE_GAUSSIAN, Interpolation::TYPE_MEAN)
        );

        $result = $this->interpolation->interpolate($interpolationConfiguration);
        $this->assertInstanceOf('AppBundle\Process\InterpolationResult', $result);
        $this->assertEquals(Interpolation::TYPE_MEAN, $result->getAlgorithm());
    }

    public function testInterpolationAlgorithmCanNotCalculate()
    {
        $interpolationConfiguration = new InterpolationParameter(
            new GridSize(10, 11),
            new BoundingBox(-10.1, 10.2, -5.1, 5.2),
            array(
                new PointValue(new Point(1, 2, 4326), 3),
                new PointValue(new Point(1, 3, 4326), 4)
            ),
            array(Interpolation::TYPE_GAUSSIAN)
        );

        $this->assertFalse($this->interpolation->interpolate($interpolationConfiguration));
    }
}