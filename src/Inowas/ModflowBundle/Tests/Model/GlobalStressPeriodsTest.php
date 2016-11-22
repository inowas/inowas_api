<?php

namespace Inowas\ModflowBundle\Tests\Model;

use Inowas\ModflowBundle\Model\GlobalStressPeriods;
use Inowas\ModflowBundle\Model\StressPeriodFactory;
use Inowas\ModflowBundle\Model\TimeUnit;

class GlobalStressPeriodsTest extends \PHPUnit_Framework_TestCase
{

    /** @var GlobalStressPeriods */
    private $globalStressPeriods;

    public function setUp(){
        $this->globalStressPeriods = new GlobalStressPeriods(
            new \DateTime('2016-01-01'),
            new \DateTime('2016-12-31'),
            TimeUnit::fromString('day')
        );
    }

    public function testInstantiate(){
        $this->assertInstanceOf(GlobalStressPeriods::class, $this->globalStressPeriods);
    }

    public function testAddOneStressperiod(){
        $this->globalStressPeriods->addStressPeriod(
            StressPeriodFactory::createRiv()
            ->setDateTimeBegin(new \DateTime('2016-01-01'))
        );

        $this->assertCount(1, $this->globalStressPeriods->getStressPeriods());
        $this->assertEquals(array(0), $this->globalStressPeriods->getTotalTimesStart());
        $this->assertEquals(365, $this->globalStressPeriods->getTotalTimeEnd());
    }

    public function testAddTwoConsecutiveStressperiods(){
        $this->globalStressPeriods->addStressPeriod(
            StressPeriodFactory::createRiv()
                ->setDateTimeBegin(new \DateTime('2016-01-01'))
        );

        $this->globalStressPeriods->addStressPeriod(
            StressPeriodFactory::createWel()
                ->setDateTimeBegin(new \DateTime('2016-01-12'))
        );

        $this->assertCount(2, $this->globalStressPeriods->getStressPeriods());
        $this->assertEquals(array(0, 11), $this->globalStressPeriods->getTotalTimesStart());
        $this->assertEquals(365, $this->globalStressPeriods->getTotalTimeEnd());
    }

    public function testAddThreeCuttingStressperiods(){
        $this->globalStressPeriods->addStressPeriod(
            StressPeriodFactory::createRiv()
                ->setDateTimeBegin(new \DateTime('2016-01-01'))
        );

        $this->globalStressPeriods->addStressPeriod(
            StressPeriodFactory::createRiv()
                ->setDateTimeBegin(new \DateTime('2016-01-16'))
        );

        $this->globalStressPeriods->addStressPeriod(
            StressPeriodFactory::createWel()
                ->setDateTimeBegin(new \DateTime('2016-01-10'))
        );

        $this->globalStressPeriods->addStressPeriod(
            StressPeriodFactory::createRch()
                ->setDateTimeBegin(new \DateTime('2016-01-11'))
        );

        $this->assertCount(4, $this->globalStressPeriods->getStressPeriods());
        $this->assertEquals(array(0, 9, 10, 15), $this->globalStressPeriods->getTotalTimesStart());
        $this->assertEquals(365, $this->globalStressPeriods->getTotalTimeEnd());
    }

    public function testGetPerlen(){
        $this->globalStressPeriods->addStressPeriod(
            StressPeriodFactory::createRiv()
                ->setDateTimeBegin(new \DateTime('2016-01-01'))
        );

        $this->globalStressPeriods->addStressPeriod(
            StressPeriodFactory::createRiv()
                ->setDateTimeBegin(new \DateTime('2016-01-16'))
        );

        $this->globalStressPeriods->addStressPeriod(
            StressPeriodFactory::createWel()
                ->setDateTimeBegin(new \DateTime('2016-01-10'))
        );

        $this->globalStressPeriods->addStressPeriod(
            StressPeriodFactory::createRch()
                ->setDateTimeBegin(new \DateTime('2016-01-11'))
        );

        $this->assertEquals(array(9, 1, 5, 350), $this->globalStressPeriods->getPerlen());
    }

    public function testGetPerlenWithOneStressPeriod(){
        $this->globalStressPeriods->addStressPeriod(
            StressPeriodFactory::createRiv()
                ->setDateTimeBegin(new \DateTime('2016-01-16'))
        );

        $this->assertEquals(array(350), $this->globalStressPeriods->getPerlen());
    }
}
