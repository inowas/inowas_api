<?php
/**
 * ipakcb : int
 * A flag that is used to determine if cell-by-cell budget data should be
 * saved. If ipakcb is non-zero cell-by-cell budget data will be saved.
 * (default is 53)
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Ipakcb
{
    /** @var int */
    private $value;

    public static function fromInteger(int $value): Ipakcb
    {
        return new self($value);
    }

    private function __construct(int $value)
    {
        $this->value = $value;
    }

    public function toValue(): int
    {
        return $this->value;
    }

    public function toInteger(): int
    {
        return $this->value;
    }
}
