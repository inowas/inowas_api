<?php
/**
 * hdry : float
 * Is the head that is assigned to cells that are converted to dry during
 * a simulation. Although this value plays no role in the model
 * calculations, it is useful as an indicator when looking at the
 * resulting heads that are output from the model. HDRY is thus similar
 * to HNOFLO in the Basic Package, which is the value assigned to cells
 * that are no-flow cells at the start of a model simulation.
 * (default is -1.e30).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Hdry
{
    /** @var float */
    private $value;

    public static function fromFloat(float $value): Hdry
    {
        return new self($value);
    }

    public static function fromValue($value): Hdry
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
