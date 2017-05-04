<?php
/**
 * backreduce : float
 * is a reduction factor used for residual control that reduces the head change
 * between nonlinear iterations. Values should be between 0.0 and 1.0, where
 * smaller values result in smaller head-change values. (default 0.7).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Backreduce
{
    /** @var float */
    private $value;

    public static function fromFloat(float $value): Backreduce
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
