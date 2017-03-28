<?php

declare(strict_types=1);

namespace Inowas\Common\Modflow;

class UnitNumber
{
    /** @var int */
    private $number;

    public static function fromInteger(int $number): UnitNumber
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

    public function toValue(): int
    {
        return $this->number;
    }
}
