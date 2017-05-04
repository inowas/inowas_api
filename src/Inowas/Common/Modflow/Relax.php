<?php
/**
 * relax : float
 * is the relaxation parameter used with npcond = 1.
 * (default is 1.0)
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Relax
{
    /** @var float */
    private $value;

    public static function fromFloat(float $value): Relax
    {
        return new self($value);
    }

    public static function fromValue(float $value): Relax
    {
        return new self($value);
    }

    private function __construct(float $value)
    {
        $this->value = $value;
    }

    public function toFloat(): float
    {
        return $this->value;
    }

    public function toValue(): float
    {
        return $this->value;
    }
}
