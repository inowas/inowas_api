<?php
/**
 * momfact : float
 * is the momentum coefficient and ranges between 0.0 and 1.0. Greater values apply
 * more weight to the head change for the current iteration. (default is 0.1).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Momfact
{
    /** @var float */
    private $value;

    public static function fromFloat(float $value): Momfact
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
