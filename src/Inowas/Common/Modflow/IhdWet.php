<?php
/**
 * ihdwet : int
 * is a flag that determines which equation is used to define the
 * initial head at cells that become wet. (default is 0)
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class IhdWet
{
    /** @var int */
    private $value;

    public static function fromInteger(int $value): IhdWet
    {
        return new self($value);
    }

    public static function fromValue(int $value): IhdWet
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
