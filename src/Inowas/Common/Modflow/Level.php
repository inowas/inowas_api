<?php
/**
 * level : int
 * (XMD) is the level of fill for incomplete LU factorization. (default is 5).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Level
{
    /** @var int */
    private $value;

    public static function fromInteger(int $value): Level
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
