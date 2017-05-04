<?php
/**
 * thickfact : float
 * is the portion of the cell thickness (length) used for smoothly
 * adjusting storage and conductance coefficients to zero.
 * (default is 1e-5).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Thickfact
{
    /** @var float */
    private $value;

    public static function fromFloat(float $value): Thickfact
    {
        return new self($value);
    }

    public static function fromValue(float $value): Thickfact
    {
        return new self($value);
    }

    private function __construct(float $value)
    {
        $this->value = $value;
    }

    public function toFloat(): float
    {
        return $this->value;
    }

    public function toValue(): float
    {
        return $this->value;
    }
}
