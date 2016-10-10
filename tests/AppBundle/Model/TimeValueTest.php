<?php

namespace Tests\AppBundle\Model;

use AppBundle\Model\RasterFactory;
use AppBundle\Model\TimeValue;

class TimeValueTest extends \PHPUnit_Framework_TestCase
{

    public function testInstantiate(){
        $this->assertInstanceOf('AppBundle\Model\TImeValue', new TimeValue());
    }

    public function testSetGetDateTime()
    {
        $dateTime = new \DateTime();
        $timeValue = new TimeValue();
        $timeValue->setDatetime($dateTime);
        $this->assertEquals($dateTime, $timeValue->getDatetime());
    }

    public function testSetGetValue()
    {
        $value = 123.1;
        $timeValue = new TimeValue();
        $timeValue->setValue($value);
        $this->assertEquals($value, $timeValue->getValue());
    }

    public function testSetGetRaster()
    {
        $raster = RasterFactory::create();
        $timeValue = new TimeValue();
        $timeValue->setRaster($raster);
        $this->assertEquals($raster, $timeValue->getRaster());
    }

}
