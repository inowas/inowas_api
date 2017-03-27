<?php
/**
 * botm : float or array of floats (nlay, nrow, ncol), optional
 * An array of the bottom elevation for each model cell
 * (the default is 0.)
 */
declare(strict_types=1);

namespace Inowas\Common\Grid;

class BottomElevation
{
    
    /** @var  array */
    protected $botm;

    public static function from3DArray(array $botm): BottomElevation
    {
        $self = new self();
        $self->botm = $botm;
        return $self;
    }

    public static function fromValue($botm): BottomElevation
    {
        $self = new self();
        $self->botm = $botm;
        return $self;
    }

    private function __construct(){}

    public function toValue()
    {
        return $this->botm;
    }
}
