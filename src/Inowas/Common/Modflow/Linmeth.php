<?php
/**
 * linmeth : int
 * is a flag that determines which matrix solver will be used.
 * A value of 1 indicates GMRES will be used
 * A value of 2 indicates XMD will be used.
 * (default is 1).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Linmeth
{
    /** @var int */
    private $value;

    public static function fromInteger(int $value): Linmeth
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
