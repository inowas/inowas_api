<?php
/**
 * backflag : int
 * is a flag used to specify whether residual control will be used. A value of 1
 * indicates that residual control is active and a value of 0 indicates residual
 * control is inactive. (default is 1).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Backflag
{
    /** @var int */
    private $value;

    public static function fromInteger(int $value): Backflag
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
