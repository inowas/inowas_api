<?php

declare(strict_types=1);

namespace Inowas\Common\DateTime;


use Inowas\Common\Modflow\Nstp;
use Inowas\Common\Modflow\Steady;
use Inowas\Common\Modflow\Tsmult;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\ModflowModel\Model\Exception\InvalidTimeUnitException;

class Stressperiod
{
    /** @var  DateTime */
    protected $dateTime;

    /** @var  DateTime */
    protected $startDateTime;

    /** @var  TimeUnit */
    protected $timeUnit;

    /** @var  TotalTime */
    protected $totim;

    /** @var  Tsmult */
    protected $tsmult;

    /** @var  Nstp */
    protected $nstp;

    /** @var  Steady */
    protected $steady;

    public static function fromDateTime(
        DateTime $dateTime,
        DateTime $startTime,
        TimeUnit $timeUnit
    ): Stressperiod
    {
        $self = new self();
        $self->dateTime = $dateTime;
        $self->startDateTime = $startTime;
        $self->timeUnit = $timeUnit;
        return $self;
    }

    public static function fromDateTimeWithTs(
        DateTime $dateTime,
        DateTime $startTime,
        TimeUnit $timeUnit,
        Nstp $nstp,
        Tsmult $tsmult,
        Steady $steady
    ): Stressperiod
    {
        $self = new self();
        $self->dateTime = $dateTime;
        $self->startDateTime = $startTime;
        $self->timeUnit = $timeUnit;
        $self->nstp = $nstp;
        $self->tsmult = $tsmult;
        $self->steady = $steady;
        return $self;
    }

    public static function fromTotimWithTs(TotalTime $totim, Nstp $nstp, Tsmult $tsmult, Steady $steady): Stressperiod
    {
        $self = new self();
        $self->totim = $totim;
        $self->nstp = $nstp;
        $self->tsmult = $tsmult;
        $self->steady = $steady;
        return $self;
    }

    public static function fromTotim(TotalTime $totim): Stressperiod
    {
        $self = new self();
        $self->totim = $totim;
        return $self;
    }

    public function dateTime(): DateTime
    {
        return $this->dateTime;
    }

    public function startDateTime(): DateTime
    {
        return $this->startDateTime;
    }

    public function timeUnit(): TimeUnit
    {
        return $this->timeUnit;
    }

    public function totalTime(): TotalTime
    {
        if ($this->totim === null) {
            $this->totim = $this->calculateTotim();
        }
        return $this->totim;
    }

    public function nstp(): Nstp
    {
        if ($this->nstp === null){
            $this->nstp = Nstp::fromInt(1);
        }
        return $this->nstp;
    }

    public function tsMult(): Tsmult
    {
        if ($this->tsmult === null){
            $this->tsmult = Tsmult::fromValue(1);
        }
        return $this->tsmult;
    }

    public function steady(): Steady
    {
        if ($this->steady === null) {
            $this->steady = Steady::fromValue(true);
        }

        return $this->steady;
    }

    private function calculateTotim(): TotalTime
    {
        $start = clone $this->startDateTime->toDateTime();
        $dateTime = clone $this->dateTime->toDateTime();
        $dateTime->modify('+1 day');
        $diff = $start->diff($dateTime);

        if ($this->timeUnit->toInt() === $this->timeUnit::SECONDS){
            return TotalTime::fromInt($dateTime->getTimestamp() - $start->getTimestamp());
        }

        if ($this->timeUnit->toInt() === $this->timeUnit::MINUTES){
            return TotalTime::fromInt((int)(($dateTime->getTimestamp() - $start->getTimestamp())/60));
        }

        if ($this->timeUnit->toInt() === $this->timeUnit::HOURS){
            return TotalTime::fromInt((int)(($dateTime->getTimestamp() - $start->getTimestamp())/60/60));
        }

        if ($this->timeUnit->toInt() === $this->timeUnit::DAYS){
            return TotalTime::fromInt((int)$diff->format("%a"));
        }

        throw InvalidTimeUnitException::withTimeUnitAndAvailableTimeUnits($this->timeUnit, $this->timeUnit->availableTimeUnits);
    }
}
