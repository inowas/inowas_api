<?php
/**
 * iwetit : int
 * is the iteration interval for attempting to wet cells. Wetting is
 * attempted every IWETIT iteration. If using the PCG solver
 * (Hill, 1990), this applies to outer iterations, not inner iterations.
 * If IWETIT  less than or equal to 0, it is changed to 1.
 * (default is 1).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class IWetIt
{
    /** @var int */
    private $value;

    public static function fromInteger(int $value): IWetIt
    {
        return new self($value);
    }

    public static function fromValue($value): IWetIt
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
