<?php
/**
 * rrctols : int
 * (XMD) is the residual reduction-convergence criteria. (default is 0.).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Rrctools
{
    /** @var float */
    private $value;

    public static function fromFloat(float $value): Rrctools
    {
        return new self($value);
    }

    private function __construct($value)
    {
        $this->value = $value;
    }

    public function toFloat(): float
    {
        return $this->value;
    }
}
