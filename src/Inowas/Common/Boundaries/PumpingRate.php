<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

class PumpingRate implements \JsonSerializable
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

    /**
     * @return array
     */
    function jsonSerialize()
    {
        return array(
            'date_time' => $this->dateTime->format(DATE_ATOM),
            'value' => $this->value,
        );
    }

    public function toArray(): array
    {
        return array(
            'date_time' => $this->dateTime->format(DATE_ATOM),
            'value' => $this->value,
        );
    }

    public static function fromArray(array $arr): PumpingRate
    {
        $self = new self();
        $self->dateTime = new \DateTimeImmutable($arr['date_time']);
        $self->value = $arr['value'];
        return $self;
    }
}
