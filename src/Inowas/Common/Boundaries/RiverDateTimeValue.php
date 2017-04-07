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
        $self = new self();
        $self->stage = $stage;
        $self->rbot = $botm;
        $self->cond = $cond;
        $self->dateTime = $dateTime;

        return $self;
    }

    public static function fromArray(array $arr): RiverDateTimeValue
    {
        $self = new self();
        $self->dateTime = new \DateTimeImmutable($arr['date_time']);
        $self->stage = $arr['stage'];
        $self->rbot = $arr['rbot'];
        $self->cond = $arr['cond'];
        return $self;
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

    public function jsonSerialize()
    {
        return $this->toArray();
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
