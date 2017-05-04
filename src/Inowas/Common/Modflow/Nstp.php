<?php
/**
 * nstp : int or array of ints (nper)
 * Number of time steps in each stress period (default is 1).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Nstp
{
    protected $ntsp;

    public static function fromArray(array $ntsp): Nstp
    {
        $self = new self();
        $self->ntsp = $ntsp;
        return $self;
    }

    public static function fromValue($ntsp): Nstp
    {
        $self = new self();
        $self->ntsp = $ntsp;
        return $self;
    }

    public static function fromInt(int $ntsp): Nstp
    {
        $self = new self();
        $self->ntsp = (int)$ntsp;
        return $self;
    }

    public function toValue()
    {
        return $this->ntsp;
    }

    public function isArray()
    {
        return is_array($this->ntsp);
    }
}
