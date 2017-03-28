<?php
/**
 * ss : float or array of floats (nlay, nrow, ncol)
 * is specific storage unless the STORAGECOEFFICIENT option is used.
 * When STORAGECOEFFICIENT is used, Ss is confined storage coefficient.
 * (default is 1.e-5).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Ss
{

    protected $value;

    public static function from3DArray(array $value): Ss
    {
        $self = new self();
        $self->value = $value;
        return $self;
    }

    public static function fromFloat(float $value): Ss
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
