<?php

/**
 * STRT—is initial (starting) head—that is, head at the beginning of the simulation.
 * STRT must be specified for all simulations, including steady-state simulations.
 * One value is read for every model cell. Usually, these values are read a layer at a time.
 * When the XSECTION option is specified, however, a single array for the cross section is read.
 * For simulations in which the first stress period is steady state, the values used for STRT
 * generally do not affect the simulation (exceptions may occur if cells go dry and (or) rewet).
 * The execution time, however, will be less if STRT includes hydraulic heads that are
 * close to the steady-state solution.
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Strt
{
    /** @var  array */
    protected $strt;

    public static function from3DArray(array $strt): Strt
    {
        $self = new self();
        $self->strt = $strt;
        return $self;
    }

    public static function fromTopAndNumberOfLayers(Top $top, int $numberOfLayers)
    {
        $strt = [];
        for ($i=0; $i<$numberOfLayers; $i++){
            $strt[$i] = $top->toValue();
        }

        $self = new self();
        $self->strt = $strt;
        return $self;
    }

    public static function fromValue($strt): Strt
    {
        $self = new self();
        $self->strt = $strt;
        return $self;
    }

    private function __construct(){}

    public function toValue()
    {
        return $this->strt;
    }
}
