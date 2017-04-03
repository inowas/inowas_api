<?php

declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Unitnumber
{
    /** @var int */
    private $number;

    public static function fromInteger(int $number): Unitnumber
    {
        return new self($number);
    }

    public static function fromValue($number): Unitnumber
    {
        return new self($number);
    }

    private function __construct($number)
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
