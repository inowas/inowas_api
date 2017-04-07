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
        $self = new self();
        $self->shead = $shead;
        $self->ehead = $ehead;
        $self->dateTime = $dateTime;

        return $self;
    }

    public static function fromArray(array $arr): ConstantHeadDateTimeValue
    {
        $self = new self();
        $self->dateTime = new \DateTimeImmutable($arr['date_time']);
        $self->shead = $arr['shead'];
        $self->ehead = $arr['ehead'];
        return $self;
    }

    public function toArray(): array
    {
        return array(
            'date_time' => $this->dateTime->format(DATE_ATOM),
            'shead' => $this->shead,
            'ehead' => $this->ehead
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
