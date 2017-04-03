<?php
/**
 * stoper : float
 * percent discrepancy that is compared to the budget percent discrepancy
 * continue when the solver convergence criteria are not met.  Execution
 * will unless the budget percent discrepancy is greater than stoper
 * (default is None). MODFLOW-2005 only
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Hnoflo
{
    /** @var float */
    protected $hnoFlo;

    public static function fromFloat(float $hnoFlo): Hnoflo
    {
        $self = new self();
        $self->hnoFlo = $hnoFlo;
        return $self;
    }

    public static function fromValue($hnoFlo): Hnoflo
    {
        $self = new self();
        $self->hnoFlo = $hnoFlo;
        return $self;
    }

    private function __construct(){}

    public function toFloat(): float
    {
        return $this->hnoFlo;
    }

    public function toValue(): float
    {
        return $this->hnoFlo;
    }
}
