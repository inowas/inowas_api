<?php
/**
 * hobdry : float
 *      Value of the simulated equivalent written to the observation output file
 *      when the observation is omitted because a cell is dry
 **/
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Hobdry
{
    protected $value;

    public static function fromFloat(float $value): Hobdry
    {
        return new self($value);
    }

    private function __construct($value)
    {
        $this->value = $value;
    }

    public function toFloat(): float
    {
        return $this->value;
    }

    public function sameAs($obj): bool
    {
        return $obj instanceof self && $obj->toFloat() === $this->value;
    }
}
