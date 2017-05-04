<?php
/**
 * iphdry : int
 * iphdry is a flag that indicates whether groundwater head will be set to
 * hdry when the groundwater head is less than 0.0001 above the cell bottom
 * (units defined by lenuni in the discretization package). If iphdry=0,
 * then head will not be set to hdry. If iphdry>0, then head will be set to
 * hdry. If the head solution from one simulation will be used as starting
 * heads for a subsequent simulation, or if the Observation Process is used
 * (Harbaugh and others, 2000), then hdry should not be printed to the output
 * file for dry cells (that is, the upw package input variable should be set
 * as iphdry=0). (default is 0)
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Iphdry
{
    /** @var int */
    private $value;

    public static function fromInt(float $value): Iphdry
    {
        return new self($value);
    }

    public static function fromValue($value): Iphdry
    {
        return new self($value);
    }

    private function __construct(float $value)
    {
        $this->value = $value;
    }

    public function toInt(): float
    {
        return $this->value;
    }

    public function toValue(): float
    {
        return $this->value;
    }
}
