<?php

declare(strict_types=1);

namespace Inowas\Common\DateTime;


use Inowas\Common\Modflow\Nstp;
use Inowas\Common\Modflow\Tsmult;
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

    /** @var  Tsmult */
    protected $tsmult;

    /** @var  Nstp */
    protected $nstp;

    public static function fromDateTime(
        DateTime $dateTime,
        DateTime $startTime,
        TimeUnit $timeUnit,
        Nstp $nstp,
        Tsmult $tsmult
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
        Nstp $nstp,
        Tsmult $tsmult
    ): StressperiodWithTimeSteps
    {
        $self = new self();
        $self->totim = $totim;
        $self->nstp = $nstp;
        $self->tsmult = $tsmult;
        return $self;
    }
}
