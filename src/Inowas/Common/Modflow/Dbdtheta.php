<?php
/**
 * dbdtheta : float
 * is a coefficient used to reduce the weight applied to the head change between
 * nonlinear iterations. dbdtheta is used to control oscillations in head.
 * Values range between 0.0 and 1.0, and larger values increase the weight
 * (decrease under-relaxation) applied to the head change. (default is 0.4).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Dbdtheta
{
    /** @var float */
    private $value;

    public static function fromFloat(float $value): Dbdtheta
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
}
