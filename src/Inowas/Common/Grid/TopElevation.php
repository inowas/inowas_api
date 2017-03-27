<?php
/**
 * top : float or array of floats (nrow, ncol), optional
 * An array of the top elevation of layer 1. For the common situation in
 * which the top layer represents a water-table aquifer, it may be
 * reasonable to set Top equal to land-surface elevation
 * (the default is 1.0)
 */
declare(strict_types=1);

namespace Inowas\Common\Grid;

class TopElevation
{

    /** @var  array */
    protected $top;

    public static function from2DArray(array $top): TopElevation
    {
        $self = new self();
        $self->top = $top;
        return $self;
    }

    public static function fromValue($top): TopElevation
    {
        $self = new self();
        $self->top = $top;
        return $self;
    }

    private function __construct(){}

    public function toValue()
    {
        return $this->top;
    }
}
