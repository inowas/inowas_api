<?php
/**
 * hclosexmd : float
 * (XMD) is the head closure criteria for inner (linear) iterations.
 * (default is 1.e-4).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Hclosexmd
{
    /** @var float */
    private $value;

    public static function fromFloat(float $value): Hclosexmd
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
}
