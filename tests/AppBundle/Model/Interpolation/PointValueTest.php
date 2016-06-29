<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Model\Interpolation\PointValue;

class PointValueTest extends \PHPUnit_Framework_TestCase
{

    public function testPointValueHasPropertyForXYValue()
    {
        $pointValue = new PointValue();
        $this->assertObjectHasAttribute('x', $pointValue);
        $this->assertObjectHasAttribute('y', $pointValue);
        $this->assertObjectHasAttribute('value', $pointValue);
    }
    
    public function testPointValueHasDefault0()
    {
        $pointValue = new PointValue();
        $this->assertEquals($pointValue->getX(), 0);
        $this->assertEquals($pointValue->getY(), 0);
        $this->assertEquals($pointValue->getValue(), 0);
    }

    public function testPointValueContructor()
    {
        $pointValue = new PointValue(1, 2, 3);
        $this->assertEquals($pointValue->getX(), 1);
        $this->assertEquals($pointValue->getY(), 2);
        $this->assertEquals($pointValue->getValue(), 3);
    }

    public function testPointValueSetter()
    {
        $pointValue = new PointValue();
        $pointValue->setX(4.1);
        $pointValue->setY(5.1);
        $pointValue->setValue(6.1);
        $this->assertEquals($pointValue->getX(), 4.1);
        $this->assertEquals($pointValue->getY(), 5.1);
        $this->assertEquals($pointValue->getValue(), 6.1);
    }
}
