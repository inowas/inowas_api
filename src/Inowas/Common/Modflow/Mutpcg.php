<?php
/**
 * mutpcg : int
 * If mutpcg = 0, tables of maximum head change and residual will be
 * printed each iteration.
 * If mutpcg = 1, only the total number of iterations will be printed.
 * If mutpcg = 2, no information will be printed.
 * If mutpcg = 3, information will only be printed if convergence fails.
 * (default is 3).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Mutpcg
{
    /** @var int */
    private $value;

    public static function fromInteger(int $value): Mutpcg
    {
        return new self($value);
    }

    public static function fromValue(int $value): Mutpcg
    {
        return new self($value);
    }

    private function __construct(int $value)
    {
        $this->value = $value;
    }

    public function toInteger(): int
    {
        return $this->value;
    }

    public function toValue(): int
    {
        return $this->value;
    }
}
