<?php
/**
 * iprpcg : int
 * solver print out interval.
 * (default is 0).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Iprpcg
{
    /** @var int */
    private $value;

    public static function fromInteger(int $value): Iprpcg
    {
        return new self($value);
    }

    public static function fromValue(int $value): Iprpcg
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
