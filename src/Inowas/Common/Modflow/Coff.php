<?php
/**
 * coff : float
 *      Fractional offset from center of cell in X direction (between columns).
 *      Default is 0.
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Coff
{
    /** @var float */
    private $value;

    public static function fromValue(float $value = 0): Coff
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
