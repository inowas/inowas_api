<?php

namespace Inowas\Flopy\Tests\Model\ValueObject;


use Inowas\Flopy\Model\ValueObject\FlopyTotalTime;
use Inowas\ModflowBundle\Model\TimeUnit;

class FlopyTotalTimeTest extends \PHPUnit_Framework_TestCase
{

    public function testDaysIntervalToInt(){
        $datetime1 = new \DateTime('2009-10-11');
        $datetime2 = new \DateTime('2011-10-18');
        $interval = $datetime1->diff($datetime2);
        $this->assertEquals(737, FlopyTotalTime::intervalToInt($interval, TimeUnit::fromString('day')));
    }

    public function testYearsIntervalToInt(){
        $datetime1 = new \DateTime('2009-10-11');
        $datetime2 = new \DateTime('2012-10-18');
        $interval = $datetime1->diff($datetime2);
        $this->assertEquals(3, FlopyTotalTime::intervalToInt($interval, TimeUnit::fromString('year')));
    }

    public function testMinutesIntervalToInt(){
        $datetime1 = new \DateTime('2009-10-11');
        $datetime2 = new \DateTime('2009-10-13');
        $interval = $datetime1->diff($datetime2);
        $this->assertEquals(2880, FlopyTotalTime::intervalToInt($interval, TimeUnit::fromString('minute')));
    }

    public function testSecondsIntervalToInt(){
        $datetime1 = new \DateTime('2009-10-11');
        $datetime2 = new \DateTime('2011-10-13');
        $interval = $datetime1->diff($datetime2);
        $this->assertEquals(63244800, FlopyTotalTime::intervalToInt($interval, TimeUnit::fromString('second')));
    }
}
