<?php
/**
 * hk : float or array of floats (nlay, nrow, ncol)
 * is the hydraulic conductivity along rows.
 * HK is multiplied by horizontal anisotropy (see CHANI and HANI)
 * to obtain hydraulic conductivity along columns.
 * (default is 1.0).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Hk
{

    protected $value;

    public static function from3DArray(array $value): Hk
    {
        $self = new self();
        $self->value = $value;
        return $self;
    }

    public static function fromValue($value): Hk
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
