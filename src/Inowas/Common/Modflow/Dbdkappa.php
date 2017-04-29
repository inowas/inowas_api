<?php
/**
 * dbdkappa : float
 * is a coefficient used to increase the weight applied to the head change between
 * nonlinear iterations. dbdkappa is used to control oscillations in head. Values
 * range between 0.0 and 1.0, and larger values increase the weight applied to the
 * head change. (default is 1.e-5).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Dbdkappa
{
    /** @var float */
    private $value;

    public static function fromFloat(float $value): Dbdkappa
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
