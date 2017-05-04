<?php
/**
 * iprnwt : int
 * is a flag that indicates whether additional information about solver
 * convergence will be printed to the main listing file.
 * (default is 0).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Iprnwt
{
    /** @var int */
    private $value;

    public static function fromInteger(int $value): Iprnwt
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
