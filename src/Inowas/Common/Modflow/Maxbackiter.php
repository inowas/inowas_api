<?php
/**
 * maxbackiter : int
 * is the maximum number of reductions (backtracks) in the head change between
 * nonlinear iterations (integer). A value between 10 and 50 works well.
 * (default is 50).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Maxbackiter
{
    /** @var int */
    private $value;

    public static function fromInteger(int $value): Maxbackiter
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
}
