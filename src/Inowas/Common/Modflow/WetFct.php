<?php
/**
 * wetfct : float
 * is a factor that is included in the calculation of the head that is
 * initially established at a cell when it is converted from dry to wet.
 * (default is 0.1).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class WetFct
{
    /** @var float */
    private $value;

    public static function fromFloat(float $value): WetFct
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
