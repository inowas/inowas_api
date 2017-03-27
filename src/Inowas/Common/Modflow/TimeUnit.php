<?php

declare(strict_types=1);

namespace Inowas\Common\Modflow;

class TimeUnit
{

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
