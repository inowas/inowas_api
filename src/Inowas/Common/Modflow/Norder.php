<?php
/**
 * norder : int
 * (XMD) is a flag for the scheme of ordering the unknowns: 0 is original ordering,
 * 1 is RCM ordering, 2 is Minimum Degree ordering. (default is 1).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Norder
{
    /** @var int */
    private $value;

    public static function fromInteger(int $value): Norder
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
