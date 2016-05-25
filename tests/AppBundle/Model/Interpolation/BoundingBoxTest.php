<?php

namespace AppBundle\Tests\Controller;


use AppBundle\Model\Interpolation\BoundingBox;

class BoundingBoxTest extends \PHPUnit_Framework_TestCase
{
    public function testBoundingBoxHasPropertyForXYValue()
    {
        $boundingBox = new BoundingBox();
        $this->assertObjectHasAttribute('xMin', $boundingBox);
        $this->assertObjectHasAttribute('xMax', $boundingBox);
        $this->assertObjectHasAttribute('yMin', $boundingBox);
        $this->assertObjectHasAttribute('yMax', $boundingBox);
    }
    
    public function testBoundingBoxHasDefault0()
    {
        $boundingBox = new BoundingBox();
        $this->assertEquals($boundingBox->getXMin(), 0);
        $this->assertEquals($boundingBox->getXMax(), 0);
        $this->assertEquals($boundingBox->getYMin(), 0);
        $this->assertEquals($boundingBox->getYMax(), 0);
    }

    public function testBoundingBoxConstructor()
    {
        $boundingBox = new BoundingBox(1, 2, 3, 4);
        $this->assertEquals($boundingBox->getXMin(), 1);
        $this->assertEquals($boundingBox->getXMax(), 2);
        $this->assertEquals($boundingBox->getYMin(), 3);
        $this->assertEquals($boundingBox->getYMax(), 4);
        $this->assertEquals($boundingBox->getYMax(), 4);
    }

    public function testPointValueSetter()
    {
        $pointValue = new BoundingBox();
        $pointValue->setXMin(4.1);
        $pointValue->setXMax(5.1);
        $pointValue->setYMin(6.1);
        $pointValue->setYMax(6.1);
        $this->assertEquals($pointValue->getXMin(), 4.1);
        $this->assertEquals($pointValue->getXMax(), 5.1);
        $this->assertEquals($pointValue->getYMin(), 6.1);
        $this->assertEquals($pointValue->getYMax(), 6.1);
    }
}
