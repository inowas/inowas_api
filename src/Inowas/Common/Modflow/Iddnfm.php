<?php
/**
 * Package ModflowOc
 *
 * is a code for the format in which heads will be printed.
 * (default is 0).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Iddnfm
{
    /** @var int */
    private $value;

    public static function fromInteger(int $value): Iddnfm
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
