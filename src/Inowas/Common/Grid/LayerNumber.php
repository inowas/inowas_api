<?php

declare(strict_types=1);

namespace Inowas\Common\Grid;

class LayerNumber
{
    /** @var int */
    private $number;

    public static function fromInteger(int $number): LayerNumber
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

    public function sameAs(LayerNumber $other)
    {
        return ($other->toInteger() === $this->toInteger());
    }
}
