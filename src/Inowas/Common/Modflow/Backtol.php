<?php
/**
 * backtol : float
 * is the proportional decrease in the root-mean-squared error of the groundwater-
 * flow equation used to determine if residual control is required at the end of
 * a nonlinear iteration. (default is 1.1).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Backtol
{
    /** @var float */
    private $value;

    public static function fromFloat(float $value): Backtol
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
