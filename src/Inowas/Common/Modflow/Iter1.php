<?php
/**
 * iter1 : int
 * maximum number of inner iterations.
 * (default is 30)
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Iter1
{
    /** @var int */
    private $value;

    public static function fromInteger(int $value): Iter1
    {
        return new self($value);
    }

    public static function fromValue(int $value): Iter1
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
