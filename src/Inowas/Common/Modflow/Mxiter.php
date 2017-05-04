<?php
/**
 * mxiter : int
 * maximum number of outer iterations.
 * (default is 50)
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Mxiter
{
    /** @var int */
    private $value;

    public static function fromInteger(int $value): Mxiter
    {
        return new self($value);
    }

    public static function fromValue(int $value): Mxiter
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
