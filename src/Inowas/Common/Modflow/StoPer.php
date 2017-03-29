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

class StoPer
{
    /** @var null|float */
    protected $stoper;

    public static function fromFloat(float $stoper): StoPer
    {
        $self = new self();
        $self->stoper = $stoper;
        return $self;
    }

    public static function fromValue($stoper): StoPer
    {
        $self = new self();
        $self->stoper = $stoper;
        return $self;
    }

    public static function none(): StoPer
    {
        return new self();
    }

    private function __construct(){}

    public function toFloat(): float
    {
        return $this->stoper;
    }

    public function toValue()
    {
        return $this->stoper;
    }
}
