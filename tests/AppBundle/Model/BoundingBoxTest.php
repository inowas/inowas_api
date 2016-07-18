<?php

namespace Tests\AppBundle\Model;

use AppBundle\Model\BoundingBox;

class BoundingBoxTest extends \PHPUnit_Framework_TestCase
{
    public function testBoundingBoxHasPropertyForXYValue()
    {
        $boundingBox = new BoundingBox();
        $this->assertObjectHasAttribute('xMin', $boundingBox);
        $this->assertObjectHasAttribute('xMax', $boundingBox);
        $this->assertObjectHasAttribute('yMin', $boundingBox);
        $this->assertObjectHasAttribute('yMax', $boundingBox);
        $this->assertObjectHasAttribute('srid', $boundingBox);
    }
    
    public function testBoundingBoxHasDefault0()
    {
        $boundingBox = new BoundingBox();
        $this->assertEquals($boundingBox->getXMin(), 0);
        $this->assertEquals($boundingBox->getXMax(), 0);
        $this->assertEquals($boundingBox->getYMin(), 0);
        $this->assertEquals($boundingBox->getYMax(), 0);
        $this->assertEquals($boundingBox->getSrid(), 0);
    }

    public function testBoundingBoxConstructor()
    {
        $boundingBox = new BoundingBox(1, 2, 3, 4, 4326);
        $this->assertEquals($boundingBox->getXMin(), 1);
        $this->assertEquals($boundingBox->getXMax(), 2);
        $this->assertEquals($boundingBox->getYMin(), 3);
        $this->assertEquals($boundingBox->getYMax(), 4);
        $this->assertEquals($boundingBox->getSrid(), 4326);
    }

    public function testPointValueSetter()
    {
        $boundingBox = new BoundingBox();
        $boundingBox->setXMin(4.1);
        $boundingBox->setXMax(5.1);
        $boundingBox->setYMin(6.1);
        $boundingBox->setYMax(6.1);
        $boundingBox->setSrid(4326);
        $this->assertEquals($boundingBox->getXMin(), 4.1);
        $this->assertEquals($boundingBox->getXMax(), 5.1);
        $this->assertEquals($boundingBox->getYMin(), 6.1);
        $this->assertEquals($boundingBox->getYMax(), 6.1);
        $this->assertEquals($boundingBox->getSrid(), 4326);
    }
}
