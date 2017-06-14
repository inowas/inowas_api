<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

class RiverDateTimeValue extends DateTimeValue
{
    const TYPE = "riv";

    /** @var float */
    private $stage;

    /** @var float */
    private $rbot;

    /** @var float */
    private $cond;

    /** @var  \DateTimeImmutable */
    private $dateTime;

    public static function fromParams(\DateTimeImmutable $dateTime, float $stage, float $botm, float $cond): RiverDateTimeValue
    {
        return new self($dateTime, $stage, $botm, $cond);
    }

    public static function fromArray(array $arr): RiverDateTimeValue
    {
        return new self(new \DateTimeImmutable($arr['date_time']), $arr['stage'], $arr['rbot'], $arr['cond']);
    }

    public static function fromArrayValues(array $arr): RiverDateTimeValue
    {
        return new self(new \DateTimeImmutable($arr[0]), $arr[1], $arr[2], $arr[3]);
    }

    private function __construct(\DateTimeImmutable $dateTime, float $stage, float $botm, float $cond)
    {
        $this->stage = $stage;
        $this->rbot = $botm;
        $this->cond = $cond;
        $this->dateTime = $dateTime;
    }

    public function toArray(): array
    {
        return array(
            'date_time' => $this->dateTime->format(DATE_ATOM),
            'stage' => $this->stage,
            'rbot' => $this->rbot,
            'cond' => $this->cond
        );
    }

    public function dateTime(): \DateTimeImmutable
    {
        return $this->dateTime;
    }

    public function rbot(): float
    {
        return $this->rbot;
    }

    public function cond(): float
    {
        return $this->cond;
    }

    public function stage(): float
    {
        return $this->stage;
    }

    public function type(): string
    {
        return self::TYPE;
    }

    public function values(): array
    {
        return array(
            'stage' => $this->stage,
            'rbot' => $this->rbot,
            'cond' => $this->cond
        );
    }
}
