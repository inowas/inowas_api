<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model;

class PumpingRate
{
    /** @var float */
    private $value;

    /** @var  \DateTimeImmutable */
    private $dateTime;

    public static function fromDateTimeAndCubicMetersPerDay(\DateTimeImmutable $dateTime, float $value): PumpingRate
    {
        $self = new self();
        $self->value = $value;
        $self->dateTime = $dateTime;

        return $self;
    }

    public static function fromCubicMetersPerDay(float $value): PumpingRate
    {
        $self = new self();
        $self->value = $value;
        $self->dateTime = null;

        return $self;
    }

    public function cubicMetersPerDay(): float
    {
        return $this->value;
    }

    public function dateTime(): ?\DateTimeImmutable
    {
        return $this->dateTime;
    }
}
