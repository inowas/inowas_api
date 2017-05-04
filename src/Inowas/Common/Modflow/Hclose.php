<?php
/**
 * hclose : float
 * is the head change criterion for convergence.
 * (default is 1e-5).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Hclose
{
    /** @var float */
    private $value;

    public static function fromFloat(float $value): Hclose
    {
        return new self($value);
    }

    public static function fromValue(float $value): Hclose
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
