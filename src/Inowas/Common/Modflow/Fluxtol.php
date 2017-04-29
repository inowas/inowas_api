<?php
/**
 * fluxtol : float
 * is the maximum l2 norm for solution of the nonlinear problem.
 * (default is 500).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Fluxtol
{
    /** @var float */
    private $value;

    public static function fromFloat(float $value): Fluxtol
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
}
