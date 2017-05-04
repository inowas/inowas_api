<?php
/**
 * headtol : float
 * is the maximum head change between outer iterations for solution of the
 * nonlinear problem. (default is 1e-4).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Headtol
{
    /** @var float */
    private $value;

    public static function fromFloat(float $value): Headtol
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
