<?php

namespace Tests\AppBundle\Model;

use AppBundle\Model\PropertyTimeValueFactory;

class PropertyTimeValueFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testInstantiate()
    {
        $this->assertInstanceOf('AppBundle\Entity\PropertyTimeValue', PropertyTimeValueFactory::create());
    }

    public function testCreateWithTime(){
        $dateTime = new \DateTime();
        $this->assertInstanceOf('AppBundle\Entity\PropertyTimeValue', PropertyTimeValueFactory::createWithTime($dateTime));
        $this->assertEquals($dateTime, PropertyTimeValueFactory::createWithTime($dateTime)->getDatetime());
    }

    public function testCreateWithTimeAndValue(){
        $dateTime = new \DateTime();
        $value = 3.81;

        $this->assertInstanceOf('AppBundle\Entity\PropertyTimeValue', PropertyTimeValueFactory::createWithTimeAndValue($dateTime, $value));
        $this->assertEquals($dateTime, PropertyTimeValueFactory::createWithTimeAndValue($dateTime, $value)->getDatetime());
        $this->assertEquals($value, PropertyTimeValueFactory::createWithTimeAndValue($dateTime, $value)->getValue());
    }
}
