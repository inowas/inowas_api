<?php
/**
 * dbdgamma : float
 * is a factor (used to weight the head change for the previous and current
 * iteration. Values range between 0.0 and 1.0, and greater values apply more weight
 * to the head change calculated during the current iteration. (default is 0.)
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Dbdgamma
{
    /** @var float */
    private $value;

    public static function fromFloat(float $value): Dbdgamma
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
