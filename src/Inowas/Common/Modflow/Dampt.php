<?php
/**
 * dampt : float
 * is the transient damping factor.
 * (default is 1.)
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Dampt
{
    /** @var float */
    private $value;

    public static function fromFloat(float $value): Dampt
    {
        return new self($value);
    }

    public static function fromValue(float $value): Dampt
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
