<?php

declare(strict_types=1);

namespace Inowas\Common\Modflow;

class TimeUnit
{

    /** @var array $availableTimeUnits */
    public $availableTimeUnits = array(1,2,3,4);

    const UNDEFINED = 0;
    const SECONDS = 1;
    const MINUTES = 2;
    const HOURS = 3;
    const DAYS = 4;
    const YEARS = 5;

    protected $itmuni;

    public static function fromInt(int $itmuni): TimeUnit
    {
        $self = new self();
        $self->itmuni = $itmuni;
        return $self;
    }

    public static function fromValue($itmuni): TimeUnit
    {
        $self = new self();
        $self->itmuni = $itmuni;
        return $self;
    }

    public function toValue()
    {
        return $this->itmuni;
    }

    public function toInt()
    {
        return $this->itmuni;
    }
}
