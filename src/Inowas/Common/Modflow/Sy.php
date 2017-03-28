<?php
/**
 * sy : float or array of floats (nlay, nrow, ncol)
 * is specific yield. (default is 0.15).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Sy
{

    protected $value;

    public static function from3DArray(array $value): Sy
    {
        $self = new self();
        $self->value = $value;
        return $self;
    }

    public static function fromFloat(float $value): Sy
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
