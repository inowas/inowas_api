<?php
/**
 * npcond : int
 * flag used to select the matrix conditioning method. (default is 1).
 * specify npcond = 1 for Modified Incomplete Cholesky.
 * specify npcond = 2 for Polynomial.
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Npcond
{
    /** @var int */
    private $value;

    public static function fromInteger(int $value): Npcond
    {
        return new self($value);
    }

    public static function fromValue(int $value): Npcond
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
