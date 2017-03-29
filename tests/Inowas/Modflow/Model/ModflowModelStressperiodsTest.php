<?php

namespace Tests\Inowas\Modflow\Model;

use Inowas\Common\DateTime\DateTime;
use Inowas\Common\DateTime\Stressperiod;
use Inowas\Common\DateTime\TotalTime;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Modflow\Model\ModflowModelStressperiods;

class ModflowModelStressperiodsTest extends \PHPUnit_Framework_TestCase
{

    /** @var  ModflowModelStressperiods */
    protected $stressPeriods;

    public function setUp(){
        $this->stressPeriods = new ModflowModelStressperiods();
    }

    public function test_adding_a_stressperiod(){
        $stressperiod = Stressperiod::fromTotim(TotalTime::fromInt(1111));
        $this->stressPeriods->addStressPeriod($stressperiod);
        $this->assertEquals(1, $this->stressPeriods->countUniqueTotims());
        $this->assertEquals(1, $this->stressPeriods->count());
    }

    public function test_calculate_totim_in_days(){
        $stressperiod = Stressperiod::fromDateTime(
            DateTime::fromDateTime(new \DateTime('2015-01-20')),
            DateTime::fromDateTime(new \DateTime('2015-01-01')),
            TimeUnit::fromValue(TimeUnit::DAYS)
        );
        $this->stressPeriods->addStressPeriod($stressperiod);
        $this->assertEquals(1, $this->stressPeriods->countUniqueTotims());
        $this->assertEquals(1, $this->stressPeriods->count());
        /** @var Stressperiod $stressperiod */
        $stressperiod = $this->stressPeriods->getStressPeriods()[0];
        $this->assertEquals(20, $stressperiod->totalTime()->toInteger());
    }

    public function test_calculate_totim_in_minutes(){
        $stressperiod = Stressperiod::fromDateTime(
            DateTime::fromDateTime(new \DateTime('2015-01-20')),
            DateTime::fromDateTime(new \DateTime('2015-01-01')),
            TimeUnit::fromValue(TimeUnit::MINUTES)
        );
        $this->stressPeriods->addStressPeriod($stressperiod);
        $this->assertEquals(1, $this->stressPeriods->countUniqueTotims());
        $this->assertEquals(1, $this->stressPeriods->count());
        /** @var Stressperiod $stressperiod */
        $stressperiod = $this->stressPeriods->getStressPeriods()[0];
        $this->assertEquals(28800, $stressperiod->totalTime()->toInteger());
    }

    public function test_adding_a_stressperiod_with_same_totim(){
        $stressperiod = Stressperiod::fromTotim(TotalTime::fromInt(1111));
        $this->stressPeriods->addStressPeriod($stressperiod);
        $this->stressPeriods->addStressPeriod($stressperiod);
        $this->assertEquals(1, $this->stressPeriods->countUniqueTotims());
        $this->assertEquals(2, $this->stressPeriods->count());
    }

    public function test_get_perlen(){
        $stressperiod = Stressperiod::fromTotim(TotalTime::fromInt(12));
        $this->stressPeriods->addStressPeriod($stressperiod);
        $stressperiod = Stressperiod::fromTotim(TotalTime::fromInt(0));
        $this->stressPeriods->addStressPeriod($stressperiod);
        $stressperiod = Stressperiod::fromTotim(TotalTime::fromInt(15));
        $this->stressPeriods->addStressPeriod($stressperiod);
        $stressperiod = Stressperiod::fromTotim(TotalTime::fromInt(15));
        $this->stressPeriods->addStressPeriod($stressperiod);
        $stressperiod = Stressperiod::fromTotim(TotalTime::fromInt(20));
        $this->stressPeriods->addStressPeriod($stressperiod);

        $expected = array(12, 3, 5);
        $this->assertEquals($expected, $this->stressPeriods->perlen());
    }
}
