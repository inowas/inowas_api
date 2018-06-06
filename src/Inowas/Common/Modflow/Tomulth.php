<?php
/**
 * tomulth : float
 *      Time step multiplier for head observations. The product of tomulth and
 *      toffset must produce a time value in units consistent with other model
 *      input. tomulth can be dimensionless or can be used to convert the units
 *      of toffset to the time unit used in the simulation.
 **/
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Tomulth
{
    protected $value;

    public static function fromFloat(float $value): Tomulth
    {
        return new self($value);
    }

    public static function fromDefault(): Tomulth
    {
        return new self(1);
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
