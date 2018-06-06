<?php

declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Unitnumber
{
    /** @var array|int */
    private $number;

    public static function fromInteger(int $number): Unitnumber
    {
        return new self($number);
    }

    public static function fromValue($number): Unitnumber
    {
        return new self($number);
    }

    public static function fromArray(array $numbers): Unitnumber
    {
        return new self($numbers);
    }

    private function __construct($number)
    {
        $this->number = $number;
    }

    public function toInteger(): int
    {
        return $this->number;
    }

    public function toValue()
    {
        return $this->number;
    }

    public function sameAs($obj): bool
    {
        return $obj instanceof self && $obj->toValue() === $this->number;
    }
}
