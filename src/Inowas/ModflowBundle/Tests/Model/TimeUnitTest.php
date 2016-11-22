<?php

namespace Inowas\ModflowBundle\Tests\Model;

use Inowas\ModflowBundle\Exception\InvalidArgumentException;
use Inowas\ModflowBundle\Model\TimeUnit;

class TimeUnitTest extends \PHPUnit_Framework_TestCase
{
    public function testFromStringWithValidValueReturnsTimeUnit(){
        $this->assertInstanceOf(TimeUnit::class, TimeUnit::fromString('day'));
    }

    public function testToStringWithValidValue(){
        $this->assertEquals('day', TimeUnit::fromString('d')->toString());
    }

    public function testFromStringWithInvalidValueThrowsException(){
        $this->expectException(InvalidArgumentException::class);
        $this->assertInstanceOf(TimeUnit::class, TimeUnit::fromString('foo'));
    }
}
