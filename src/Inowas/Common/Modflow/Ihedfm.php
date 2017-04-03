<?php
/**
 * Package ModflowOc
 *
 * ihedfm : int
 * is a code for the format in which heads will be printed.
 * (default is 0).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Ihedfm
{
    /** @var int */
    private $value;

    public static function fromInteger(int $value): Ihedfm
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
