<?php
/**
 * hani : float or array of floats (nlay, nrow, ncol)
 * is the ratio of hydraulic conductivity along columns to hydraulic
 * conductivity along rows, where HK of item 10 specifies the hydraulic
 * conductivity along rows. Thus, the hydraulic conductivity along
 * columns is the product of the values in HK and HANI.
 * (default is 1.0).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Hani
{

    protected $value;

    public static function from3DArray(array $value): Hani
    {
        $self = new self();
        $self->value = $value;
        return $self;
    }

    public static function fromValue($value): Hani
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
