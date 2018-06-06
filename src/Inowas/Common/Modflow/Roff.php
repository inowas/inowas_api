<?php
/**
 * roff : float
 *      Fractional offset from center of cell in Y direction (between rows).
 *      Default is 0.
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Roff
{
    /** @var float */
    private $value;

    public static function fromValue(float $value = 0): Roff
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
