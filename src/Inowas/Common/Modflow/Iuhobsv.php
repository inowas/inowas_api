<?php
/**
 * iuhobsv : int
 * unit number where output is saved
 * default ist 51, when oc-package is on, another unitnumber like 1051 should be given
 * more info here: https://github.com/modflowpy/flopy/issues/235
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Iuhobsv
{
    /** @var int */
    private $value;

    public static function fromInt(int $value): Iuhobsv
    {
        return new self($value);
    }

    private function __construct(int $value)
    {
        $this->value = $value;
    }

    public function toInt(): int
    {
        return $this->value;
    }

    public function sameAs($obj): bool
    {
        return $obj instanceof self && $obj->toInt() === $this->value;
    }
}
