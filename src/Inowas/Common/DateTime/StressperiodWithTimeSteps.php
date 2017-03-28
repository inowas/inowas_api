<?php

declare(strict_types=1);

namespace Inowas\Common\DateTime;


use Inowas\Common\Modflow\NumberOfTimeSteps;
use Inowas\Common\Modflow\TimeStepMultiplier;
use Inowas\Common\Modflow\TimeUnit;

class StressperiodWithTimeSteps
{
    /** @var  DateTime */
    protected $dateTime;

    /** @var  DateTime */
    protected $startDateTime;

    /** @var  TimeUnit */
    protected $timeUnit;

    /** @var  TotalTime */
    protected $totim;

    /** @var  TimeStepMultiplier */
    protected $tsmult;

    /** @var  NumberOfTimeSteps */
    protected $nstp;

    public static function fromDateTime(
        DateTime $dateTime,
        DateTime $startTime,
        TimeUnit $timeUnit,
        NumberOfTimeSteps $nstp,
        TimeStepMultiplier $tsmult
    ): StressperiodWithTimeSteps
    {
        $self = new self();
        $self->dateTime = $dateTime;
        $self->startDateTime = $startTime;
        $self->timeUnit = $timeUnit;
        $self->nstp = $nstp;
        $self->tsmult = $tsmult;
        return $self;
    }

    public static function fromTotim(
        TotalTime $totim,
        NumberOfTimeSteps $nstp,
        TimeStepMultiplier $tsmult
    ): StressperiodWithTimeSteps
    {
        $self = new self();
        $self->totim = $totim;
        $self->nstp = $nstp;
        $self->tsmult = $tsmult;
        return $self;
    }
}
