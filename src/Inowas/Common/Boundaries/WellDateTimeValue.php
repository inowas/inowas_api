<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

class WellDateTimeValue extends DateTimeValue
{

    const TYPE = 'wel';

    /** @var float */
    private $pumpingRate;

    /** @var  \DateTimeImmutable */
    private $dateTime;


    public static function fromParams(\DateTimeImmutable $dateTime, float $pumpingRate): WellDateTimeValue
    {
        $self = new self();
        $self->dateTime = $dateTime;
        $self->pumpingRate = $pumpingRate;
        return $self;
    }

    public static function fromArray(array $arr): WellDateTimeValue
    {
        $self = new self();
        $self->dateTime = new \DateTimeImmutable($arr['date_time']);
        $self->pumpingRate = $arr['pumping_rate'];
        return $self;
    }

    private function __construct(){}

    public function type(): string
    {
        return self::TYPE;
    }


    public function dateTime(): \DateTimeImmutable
    {
        return $this->dateTime;
    }

    public function pumpingRate(): float
    {
        return $this->pumpingRate;
    }

    public function toArray(): array
    {
        return array(
            'date_time' => $this->dateTime->format(DATE_ATOM),
            'pumping_rate' => $this->pumpingRate
        );
    }

    function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function values(): array
    {
        return array(
            'pumping_rate' => $this->pumpingRate
        );
    }
}
