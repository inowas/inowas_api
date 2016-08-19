<?php

namespace Tests\AppBundle\Model;


use AppBundle\Model\StressPeriod;

class StressPeriodTest extends \PHPUnit_Framework_TestCase
{

    public function testInstantiateWithDefaultValues()
    {
        $stressPeriod = new StressPeriod();
        $this->assertInstanceOf('AppBundle\Model\StressPeriod', $stressPeriod);
        $this->assertEquals(null, $stressPeriod->getDateTimeBegin());
        $this->assertEquals(null, $stressPeriod->getDateTimeEnd());
        $this->assertEquals(1, $stressPeriod->getNumberOfTimeSteps());
        $this->assertEquals(true, $stressPeriod->isSteady());
    }

    public function testInstantiateWithValues()
    {
        $dateBegin = new \DateTime();
        $dateEnd = new \DateTime();
        $numberOfTimeSteps = 2;
        $steady = false;

        $stressPeriod = new StressPeriod($dateBegin, $dateEnd, $numberOfTimeSteps, $steady);
        $this->assertInstanceOf('AppBundle\Model\StressPeriod', $stressPeriod);
        $this->assertEquals($dateBegin, $stressPeriod->getDateTimeBegin());
        $this->assertEquals($dateEnd, $stressPeriod->getDateTimeEnd());
        $this->assertEquals($numberOfTimeSteps, $stressPeriod->getNumberOfTimeSteps());
        $this->assertEquals($steady, $stressPeriod->isSteady());
    }

    public function testSetGetDateBegin()
    {
        $dateBegin = new \DateTime();
        $stressPeriod = new StressPeriod();
        $stressPeriod->setDateTimeBegin($dateBegin);
        $this->assertEquals($dateBegin, $stressPeriod->getDateTimeBegin());
    }

    public function testSetGetDateEnd()
    {
        $dateEnd = new \DateTime();
        $stressPeriod = new StressPeriod();
        $stressPeriod->setDateTimeEnd($dateEnd);
        $this->assertEquals($dateEnd, $stressPeriod->getDateTimeEnd());
    }

    public function testSetGetNumberOfTimeSteps()
    {
        $numberOfTimeSteps = 2;
        $stressPeriod = new StressPeriod();
        $stressPeriod->setNumberOfTimeSteps($numberOfTimeSteps);
        $this->assertEquals($numberOfTimeSteps, $stressPeriod->getNumberOfTimeSteps());
    }

    public function testSetGetSteady()
    {
        $steady = false;
        $stressPeriod = new StressPeriod();
        $stressPeriod->setSteady($steady);
        $this->assertEquals($steady, $stressPeriod->isSteady());
    }

    public function testGetLength(){
        $dateBegin = new \DateTime('2015-01-01', new \DateTimeZone('Europe/Berlin'));
        $dateEnd = new \DateTime('2016-02-12', new \DateTimeZone('Europe/Berlin'));
        $numberOfTimeSteps = 2;
        $steady = false;

        $stressPeriod = new StressPeriod($dateBegin, $dateEnd, $numberOfTimeSteps, $steady);
        $this->assertEquals(407, $stressPeriod->getLengthInDays());
    }

    public function testSerialize(){
        $dateBegin = new \DateTime('2015-01-01', new \DateTimeZone('Europe/Berlin'));
        $dateEnd = new \DateTime('2015-01-02', new \DateTimeZone('Europe/Berlin'));
        $numberOfTimeSteps = 2;
        $steady = false;

        $stressPeriod = new StressPeriod($dateBegin, $dateEnd, $numberOfTimeSteps, $steady);
        $expected = json_decode('{"dateTimeBegin":{"date":"2015-01-01 00:00:00.000000","timezone_type":3,"timezone":"Europe\/Berlin"},"dateTimeEnd":{"date":"2015-01-02 00:00:00.000000","timezone_type":3,"timezone":"Europe\/Berlin"},"numberOfTimeSteps":2,"steady":false,"timeStepMultiplier":1}');
        $this->assertEquals($expected, json_decode(json_encode($stressPeriod)));
    }
}
