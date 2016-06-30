<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Model\Interpolation\PointValue;
use AppBundle\Model\Point;

class PointValueTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiate()
    {
        $pointValue = new PointValue(new Point(12, 13, 4326), 123.1);
        $this->assertObjectHasAttribute('value', $pointValue);
        $this->assertEquals($pointValue->getX(), 12);
        $this->assertEquals($pointValue->getY(), 13);
        $this->assertEquals($pointValue->getValue(), 123.1);
    }
}
