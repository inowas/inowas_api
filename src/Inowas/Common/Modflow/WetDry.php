<?php
/**
 * wetdry : float or array of floats (nlay, nrow, ncol)
 * is a combination of the wetting threshold and a flag to indicate
 * which neighboring cells can cause a cell to become wet.
 * (default is -0.01).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class WetDry
{

    protected $value;

    public static function from3DArray(array $value): WetDry
    {
        $self = new self();
        $self->value = $value;
        return $self;
    }

    public static function fromFloat(float $value): WetDry
    {
        $self = new self();
        $self->value = $value;
        return $self;
    }

    public static function fromValue($value): WetDry
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
