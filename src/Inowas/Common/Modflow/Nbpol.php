<?php
/**
 * nbpol : int
 * is only used when npcond = 2 to indicate whether the estimate of the
 * upper bound on the maximum eigenvalue is 2.0, or whether the estimate
 * will be calculated. nbpol = 2 is used to specify the value is 2.0;
 * for any other value of nbpol, the estimate is calculated. Convergence
 * is generally insensitive to this parameter.
 * (default is 2).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Nbpol
{
    /** @var int */
    private $value;

    public static function fromInteger(int $value): Nbpol
    {
        return new self($value);
    }

    public static function fromValue(int $value): Nbpol
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
