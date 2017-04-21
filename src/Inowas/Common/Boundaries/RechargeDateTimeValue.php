<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

class RechargeDateTimeValue extends DateTimeValue
{

    const TYPE = 'rch';

    /** @var float */
    private $rechargeRate;

    /** @var  \DateTimeImmutable */
    private $dateTime;


    public static function fromParams(\DateTimeImmutable $dateTime, float $rechargeRate): RechargeDateTimeValue
    {
        $self = new self();
        $self->dateTime = $dateTime;
        $self->rechargeRate = $rechargeRate;
        return $self;
    }

    public static function fromArray(array $arr): RechargeDateTimeValue
    {
        $self = new self();
        $self->dateTime = new \DateTimeImmutable($arr['date_time']);
        $self->rechargeRate = $arr['recharge_rate'];
        return $self;
    }

    private function __construct(){}

    public function rechargeRate(): float
    {
        return $this->rechargeRate;
    }

    public function type(): string
    {
        return self::TYPE;
    }

    public function dateTime(): \DateTimeImmutable
    {
        return $this->dateTime;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return array(
            'date_time' => $this->dateTime->format(DATE_ATOM),
            'recharge_rate' => $this->rechargeRate
        );
    }

    public function values(): array
    {
        return array(
            'recharge_rate' => $this->rechargeRate
        );
    }
}
