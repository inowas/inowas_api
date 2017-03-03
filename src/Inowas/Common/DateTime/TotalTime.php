<?php

namespace Inowas\Common\DateTime;

class TotalTime implements \JsonSerializable
{
    /** @var int */
    private $totalTime;

    public static function fromInt(int $totalTime): TotalTime
    {
        $self = new self();
        $self->totalTime = $totalTime;
        return $self;
    }

    public function toInteger(): int
    {
        return $this->totalTime;
    }

    function jsonSerialize()
    {
        return $this->toInteger();
    }
}
