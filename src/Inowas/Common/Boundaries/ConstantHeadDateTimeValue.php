<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

class ConstantHeadDateTimeValue extends DateTimeValue
{

    const TYPE = "chd";

    /** @var  \DateTimeImmutable */
    private $dateTime;

    /** @var  float */
    protected $shead;

    /** @var  float */
    protected $ehead;

    public static function fromParams(\DateTimeImmutable $dateTime, float $shead, float $ehead): ConstantHeadDateTimeValue
    {
        return new self($dateTime, $shead, $ehead);
    }

    public static function fromArray(array $arr): ConstantHeadDateTimeValue
    {
        return new self(new \DateTimeImmutable($arr['date_time']), $arr['shead'], $arr['ehead']);
    }

    public static function fromArrayValues(array $arr): ConstantHeadDateTimeValue
    {
        return new self(new \DateTimeImmutable($arr[0]), $arr[1], $arr[2]);
    }

    private function __construct(\DateTimeImmutable $dateTime, float $shead, float $ehead)
    {
        $this->dateTime = $dateTime;
        $this->shead = $shead;
        $this->ehead = $ehead;
    }

    public function toArray(): array
    {
        return array(
            'date_time' => $this->dateTime->format(DATE_ATOM),
            'shead' => $this->shead,
            'ehead' => $this->ehead
        );
    }

    public function toArrayValues(): array
    {
        return array($this->dateTime->format(DATE_ATOM), $this->shead, $this->ehead);
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function dateTime(): \DateTimeImmutable
    {
        return $this->dateTime;
    }

    public function shead(): float
    {
        return $this->shead;
    }

    public function ehead(): float
    {
        return $this->ehead;
    }

    public function type(): string
    {
        return self::TYPE;
    }

    public function values(): array
    {
        return array(
            'shead' => $this->shead,
            'ehead' => $this->ehead
        );
    }
}
