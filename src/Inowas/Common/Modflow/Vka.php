<?php
/**
 * vka : float or array of floats (nlay, nrow, ncol)
 * is either vertical hydraulic conductivity or the ratio of horizontal
 * to vertical hydraulic conductivity depending on the value of LAYVKA.
 * (default is 1.0).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Vka
{

    protected $value;

    public static function from3DArray(array $value): Vka
    {
        $self = new self();
        $self->value = $value;
        return $self;
    }

    public static function fromValue($value): Vka
    {
        $self = new self();
        $self->value = $value;
        return $self;
    }

    public static function fromFloat(float $value): Vka
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
