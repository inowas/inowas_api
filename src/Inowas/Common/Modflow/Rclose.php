<?php
/**
 * rclose : float
 * is the residual criterion for convergence.
 * (default is 1e-5)
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Rclose
{
    /** @var float */
    private $value;

    public static function fromFloat(float $value): Rclose
    {
        return new self($value);
    }

    public static function fromValue(float $value): Rclose
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
