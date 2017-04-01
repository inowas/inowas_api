<?php
/**
 * tsmult : float or array of floats (nper)
 * Time step multiplier (default is 1.0).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Tsmult
{
    protected $tsmult;

    public static function fromArray(array $tsmult): Tsmult
    {
        $self = new self();
        $self->tsmult = $tsmult;
        return $self;
    }

    public static function fromValue($tsmult): Tsmult
    {
        $self = new self();
        $self->tsmult = $tsmult;
        return $self;
    }

    public function toValue()
    {
        return $this->tsmult;
    }

    public function isArray()
    {
        return is_array($this->tsmult);
    }
}
