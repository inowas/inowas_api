<?php
/**
 * maxiterout : int
 * is the maximum number of iterations to be allowed for solution of the
 * outer (nonlinear) problem. (default is 100).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Maxiterout
{
    /** @var int */
    private $value;

    public static function fromInteger(int $value): Maxiterout
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
