<?php

namespace Inowas\ModflowBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Inowas\Flopy\Model\ValueObject\FlopyTotalTime;
use Inowas\ModflowBundle\Model\Boundary\StressPeriod;

class GlobalStressPeriods
{
    /** @var  TimeUnit */
    private $timeUnit;

    /** @var \DateTime */
    private $start;

    /** @var \DateTime */
    private $end;

    /** @var ArrayCollection */
    private $stressPeriods;

    /** @var array */
    private $totalTimesStart = array();

    /** @var integer */
    private $totalTimeEnd;

    public function __construct(\DateTime $start, \DateTime $end, TimeUnit $timeUnit) {
        $this->timeUnit = $timeUnit;
        $this->stressPeriods = new ArrayCollection();
        $this->start = $start;
        $this->end = $end;
    }

    /**
     * @param StressPeriod $stressPeriod
     * @return $this
     */
    public function addStressPeriod(StressPeriod $stressPeriod){
        $this->stressPeriods[] = $stressPeriod;
        $this->calculate();
        return $this;
    }

    private function calculate(){
        $startTotalTimes = [];
        /** @var StressPeriod $stressPeriod */
        foreach ($this->stressPeriods as $stressPeriod){
            if ($stressPeriod->getDateTimeBegin() >= $this->start && $stressPeriod->getDateTimeBegin() <= $this->end) {
                $startTotalTimes[] = $stressPeriod->getTotalTimeStart($this->start, $this->timeUnit);
            }
        }

        $this->totalTimesStart = array_unique($startTotalTimes);
        usort($this->totalTimesStart, function($a, $b) {return ($a < $b) ? -1 : 1;});
        $this->totalTimeEnd = FlopyTotalTime::beginEndToInt($this->start, $this->end, $this->timeUnit);
    }

    /**
     * @return TimeUnit
     */
    public function getTimeUnit(): TimeUnit
    {
        return $this->timeUnit;
    }

    /**
     * @return \DateTime
     */
    public function getStart(): \DateTime
    {
        return $this->start;
    }

    /**
     * @return \DateTime
     */
    public function getEnd(): \DateTime
    {
        return $this->end;
    }

    /**
     * @return ArrayCollection
     */
    public function getStressPeriods(): ArrayCollection
    {
        return $this->stressPeriods;
    }

    /**
     * @return array
     */
    public function getTotalTimesStart(): array
    {
        return $this->totalTimesStart;
    }

    /**
     * @return int
     */
    public function getTotalTimeEnd(): int
    {
        return $this->totalTimeEnd;
    }

    /**
     * @return array
     */
    public function getPerlen(){

        $perlen = array();
        $numberOfStressPeriods = count($this->totalTimesStart);

        $totalTimesStartAnEnd = $this->totalTimesStart;
        $totalTimesStartAnEnd[] = $this->totalTimeEnd;

        for ($i = 0; $i<$numberOfStressPeriods; $i++){
            $perlen[] = $totalTimesStartAnEnd[$i+1]-$totalTimesStartAnEnd[$i];
        }

        return $perlen;
    }

    /**
     * @return array
     */
    public function getNstp(){
        $nstp = array();
        $numberOfStressPeriods = count($this->totalTimesStart);
        for ($i = 0; $i<$numberOfStressPeriods; $i++){
            $nstp[] = 1;
        }

        return $nstp;
    }

    /**
     * @return array
     */
    public function getTsmult(){
        $tsmult = array();
        $numberOfStressPeriods = count($this->totalTimesStart);
        for ($i = 0; $i<$numberOfStressPeriods; $i++){
            $tsmult[] = 1;
        }

        return $tsmult;
    }

    /**
     * @return array
     */
    public function getSteady(){
        $steady = array();
        $numberOfStressPeriods = count($this->totalTimesStart);
        for ($i = 0; $i<$numberOfStressPeriods; $i++){
            $steady[] = false;
        }

        return $steady;
    }
}
