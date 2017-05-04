<?php

declare(strict_types=1);

namespace Inowas\Common\Modflow;

class LengthUnit
{
    
    const UNDEFINED = 0;
    const FEET = 1;
    const METERS = 2;
    const CENTIMETERS = 3;

    protected $lenuni;

    public static function fromInt(int $lenuni): LengthUnit
    {
        $self = new self();
        $self->lenuni = $lenuni;
        return $self;
    }

    public static function fromValue(int $lenuni): LengthUnit
    {
        $self = new self();
        $self->lenuni = $lenuni;
        return $self;
    }

    public function toValue(): int
    {
        return $this->lenuni;
    }

    public function toInt(): int
    {
        return $this->lenuni;
    }

    public function sameAs(LengthUnit $lengthUnit): bool
    {
        return $this->lenuni == $lengthUnit->toInt();
    }
}
