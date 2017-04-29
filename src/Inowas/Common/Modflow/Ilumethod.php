<?php
/**
 * ilumethod : int
 * (GMRES) is the index for selection of the method for incomplete factorization
 * (ILU) used as a preconditioner. (default is 2).
 *
 * ilumethod = 1 is ILU with drop tolerance and fill limit. Fill-in terms less
 * than drop tolerance times the diagonal are discarded. The number of fill-in
 * terms in each row of L and U is limited to the fill limit. The fill-limit
 * largest elements are kept in the L and U factors.
 *
 * ilumethod=2 is ILU(k) order k incomplete LU factorization. Fill-in terms of
 * higher order than k in the factorization are discarded.
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Ilumethod
{
    /** @var int */
    private $value;

    public static function fromInteger(int $value): Ilumethod
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
}
