<?php
/**
 * chani : float or array of floats (nlay)
 * contains a value for each layer that is a flag or the horizontal
 * anisotropy. If CHANI is less than or equal to 0, then variable HANI
 * defines horizontal anisotropy. If CHANI is greater than 0, then CHANI
 * is the horizontal anisotropy for the entire layer, and HANI is not
 * read. If any HANI parameters are used, CHANI for all layers must be
 * less than or equal to 0. Use as many records as needed to enter a
 * value of CHANI for each layer. The horizontal anisotropy is the ratio
 * of the hydraulic conductivity along columns (the Y direction) to the
 * hydraulic conductivity along rows (the X direction).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Chani
{
    protected $chani;

    public static function fromArray(array $chani): Chani
    {
        $self = new self();
        $self->chani = $chani;
        return $self;
    }

    public static function fromFloat(float $chani): Chani
    {
        $self = new self();
        $self->chani = $chani;
        return $self;
    }

    public static function fromValue($chani): Chani
    {
        $self = new self();
        $self->chani = $chani;
        return $self;
    }

    public function toValue()
    {
        return $this->chani;
    }

    public function isArray()
    {
        return is_array($this->chani);
    }
}
