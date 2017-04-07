<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

class GeneralHeadDateTimeValue extends DateTimeValue
{

    const TYPE = "ghb";

    /** @var float */
    private $stage;

    /** @var float */
    private $cond;

    /** @var  \DateTimeImmutable */
    private $dateTime;

    public static function fromParams(\DateTimeImmutable $dateTime, float $stage, float $cond): GeneralHeadDateTimeValue
    {
        $self = new self();
        $self->stage = $stage;
        $self->cond = $cond;
        $self->dateTime = $dateTime;

        return $self;
    }

    public static function fromArray(array $arr): GeneralHeadDateTimeValue
    {
        $self = new self();
        $self->dateTime = new \DateTimeImmutable($arr['date_time']);
        $self->stage = $arr['stage'];
        $self->cond = $arr['cond'];
        return $self;
    }

    public function toArray(): array
    {
        return array(
            'date_time' => $this->dateTime->format(DATE_ATOM),
            'stage' => $this->stage,
            'cond' => $this->cond
        );
    }

    public function jsonSerialize()
    {
        return $this->toArray();
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
