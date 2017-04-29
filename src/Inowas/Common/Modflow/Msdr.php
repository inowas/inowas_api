<?php
/**
 * msdr : int
 * (GMRES) is the number of iterations between restarts of the GMRES Solver.
 * (default is 15).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Msdr
{
    /** @var int */
    private $value;

    public static function fromInteger(int $value): Msdr
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
