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
        return new self($dateTime, $pumpingRate);
    }

    public static function fromArray(array $arr): WellDateTimeValue
    {
        return new self(new \DateTimeImmutable($arr['date_time']), $arr['pumping_rate']);
    }

    public static function fromArrayValues(array $arr): WellDateTimeValue
    {
        return new self(new \DateTimeImmutable($arr[0]), $arr[1]);
    }

    private function __construct(\DateTimeImmutable $dateTime, float $pumpingRate){
        $this->dateTime = $dateTime;
        $this->pumpingRate = $pumpingRate;
    }

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

    public function toArrayValues(): array
    {
        return array($this->dateTime->format(DATE_ATOM), $this->pumpingRate);
    }

    public function values(): array
    {
        return array(
            'pumping_rate' => $this->pumpingRate
        );
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
