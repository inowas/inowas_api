<?php
/**
 * epsrn : float
 * (XMD) is the drop tolerance for preconditioning. (default is 1.e-4).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Epsrn
{
    /** @var float */
    private $value;

    public static function fromFloat(float $value): Epsrn
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
