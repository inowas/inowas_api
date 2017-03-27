<?php

declare(strict_types=1);

namespace Inowas\Common\Modflow;

class TimePeriodsNumber
{
    /** @var int */
    private $number;

    public static function fromInteger(int $number): TimePeriodsNumber
    {
        return new self($number);
    }

    private function __construct(int $number)
    {
        $this->number = $number;
    }

    public function toInteger(): int
    {
        return $this->number;
    }

    public function sameAs(TimePeriodsNumber $other)
    {
        return ($other->toInteger() === $this->toInteger());
    }
}
