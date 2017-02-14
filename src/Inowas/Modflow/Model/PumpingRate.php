<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model;

class PumpingRate
{
    /** @var float */
    private $number;

    public static function fromValue(float $number): PumpingRate
    {
        return new self($number);
    }

    private function __construct(float $number)
    {
        $this->number = $number;
    }

    public function toFloat(): float
    {
        return $this->number;
    }
}
