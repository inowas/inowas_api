<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

class GeneralHeadDateTimeValue extends DateTimeValue
{

    const TYPE = 'ghb';

    /** @var float */
    private $stage;

    /** @var float */
    private $cond;

    /** @var  \DateTimeImmutable */
    private $dateTime;

    public static function fromParams(\DateTimeImmutable $dateTime, float $stage, float $cond): GeneralHeadDateTimeValue
    {
        return new self($dateTime, $stage, $cond);
    }

    public static function fromArray(array $arr): GeneralHeadDateTimeValue
    {
        return new self(new \DateTimeImmutable($arr['date_time']), $arr['stage'], $arr['cond']);
    }

    public static function fromArrayValues(array $arr): GeneralHeadDateTimeValue
    {
        return new self(new \DateTimeImmutable($arr[0]), $arr[1], $arr[2]);
    }

    private function __construct(\DateTimeImmutable $dateTime, float $stage, float $cond)
    {
        $this->dateTime = $dateTime;
        $this->stage = $stage;
        $this->cond = $cond;
    }

    public function toArray(): array
    {
        return array(
            'date_time' => $this->dateTime->format(DATE_ATOM),
            'stage' => $this->stage,
            'cond' => $this->cond
        );
    }

    public function dateTime(): \DateTimeImmutable
    {
        return $this->dateTime;
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
            'cond' => $this->cond
        );
    }
}
