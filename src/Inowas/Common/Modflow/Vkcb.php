<?php
/**
 * vkcb : float or array of floats (nlay, nrow, ncol)
 * is the vertical hydraulic conductivity of a Quasi-three-dimensional
 * confining bed below a layer.
 * (default is 0.0).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Vkcb
{

    protected $value;

    public static function from3DArray(array $value): Vkcb
    {
        $self = new self();
        $self->value = $value;
        return $self;
    }

    public static function fromFloat(float $value): Vkcb
    {
        $self = new self();
        $self->value = $value;
        return $self;
    }

    private function __construct(){}

    public function toValue()
    {
        return $this->value;
    }
}
