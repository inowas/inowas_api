<?php
/**
 * stoptol : float
 * (GMRES) is the tolerance for convergence of the linear solver. This is the
 * residual of the linear equations scaled by the norm of the root mean squared
 * error. Usually 1.e-8 to 1.e-12 works well. (default is 1.e-10).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Stoptol
{
    /** @var float */
    private $value;

    public static function fromFloat(float $value): Stoptol
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
