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
        return new self($dateTime, $rechargeRate);
    }

    public static function fromArray(array $arr): RechargeDateTimeValue
    {
        return new self(new \DateTimeImmutable($arr['date_time']), $arr['recharge_rate']);
    }

    public static function fromArrayValues(array $arr): RechargeDateTimeValue
    {
        return new self(new \DateTimeImmutable($arr[0]), $arr[1]);
    }

    private function __construct(\DateTimeImmutable $dateTime, float $rechargeRate) {
        $this->dateTime = $dateTime;
        $this->rechargeRate = $rechargeRate;
    }

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

    public function toArrayValues(): array
    {
        return array(
            $this->dateTime->format(DATE_ATOM),
            $this->rechargeRate
        );
    }

    public function values(): array
    {
        return array(
            'recharge_rate' => $this->rechargeRate
        );
    }
}
