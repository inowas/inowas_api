<?php
/**
 * tsmult : float or array of floats (nper)
 * Time step multiplier (default is 1.0).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class TimeStepMultiplier
{
    protected $tsmult;

    public static function fromArray(array $tsmult): TimeStepMultiplier
    {
        $self = new self();
        $self->tsmult = $tsmult;
        return $self;
    }

    public static function fromValue($tsmult): TimeStepMultiplier
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
