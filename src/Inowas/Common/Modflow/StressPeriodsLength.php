<?php
/**
 * perlen : float or array of floats (nper)
 * An array of the stress period lengths.
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class StressPeriodsLength
{
    protected $perlen;

    public static function fromArray(array $perlen): StressPeriodsLength
    {
        $self = new self();
        $self->perlen = $perlen;
        return $self;
    }

    public static function fromValue($perlen): StressPeriodsLength
    {
        $self = new self();
        $self->perlen = $perlen;
        return $self;
    }

    public function toValue()
    {
        return $this->perlen;
    }

    public function isArray()
    {
        return is_array($this->perlen);
    }
}
