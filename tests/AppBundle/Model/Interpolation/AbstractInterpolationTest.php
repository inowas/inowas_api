<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Model\Interpolation\AbstractInterpolation;
use AppBundle\Model\Interpolation\BoundingBox;
use AppBundle\Model\Interpolation\GridSize;
use AppBundle\Model\Interpolation\MeanInterpolation;
use AppBundle\Model\Interpolation\PointValue;
use AppBundle\Model\Point;
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
        $this->serializer = SerializerBuilder::create()->build();

        $this->interpolation = new MeanInterpolation(
            new GridSize(3,4),
            new BoundingBox(12, 12, 13, 13)
        );
    }

    public function testInstantiate(){
        $this->assertInstanceOf('AppBundle\Model\Interpolation\AbstractInterpolation', $this->interpolation);
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $this->interpolation->getPointValues());
        $this->assertEquals(new GridSize(3, 4), $this->interpolation->getGridSize());
        $this->assertEquals(new BoundingBox(12, 12, 13, 13), $this->interpolation->getBoundingBox());
    }

    public function addGetRemovePointValues(){
        $pointValue = new PointValue(new Point(12, 12, 4326), 123.1);
        $this->assertCount(0, $this->interpolation->getPointValues());
        $this->interpolation->addPointValue($pointValue);
        $this->assertCount(1, $this->interpolation->getPointValues());
        $this->interpolation->addPointValue($pointValue);
        $this->assertCount(1, $this->interpolation->getPointValues());
        $anotherPointValue = new PointValue(new Point(14, 12, 4326), 123.1);
        $this->interpolation->addPointValue($anotherPointValue);
        $this->assertCount(2, $this->interpolation->getPointValues());
        $this->interpolation->removePointValue($pointValue);
        $this->interpolation->removePointValue($anotherPointValue);
        $this->assertCount(0, $this->interpolation->getPointValues());
    }
}
