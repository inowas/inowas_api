<?php
/**
 * irch : int or array of ints (nrow, ncol)
 * is the layer to which recharge is applied in each vertical
 * column (only used when nrchop=2).
 * (default is 0).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Irch
{
    /** @var array|int */
    private $value;

    public static function fromInteger(int $value): Irch
    {
        return new self($value);
    }

    public static function fromArray(array $value): Irch
    {
        return new self($value);
    }

    public static function fromValue($value): Irch
    {
        return new self($value);
    }

    private function __construct($value)
    {
        $this->value = $value;
    }

    public function toValue()
    {
        return $this->value;
    }
}
