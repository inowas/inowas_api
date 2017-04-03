<?php
/**
 * layvka : float or array of floats (nlay)
 * a flag for each layer that indicates whether variable VKA is vertical
 * hydraulic conductivity or the ratio of horizontal to vertical
 * hydraulic conductivity.
 **/
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Layvka
{
    protected $layvka;

    public static function fromArray(array $layvka): Layvka
    {
        $self = new self();
        $self->layvka = $layvka;
        return $self;
    }

    public static function fromFloat(float $layvka): Layvka
    {
        $self = new self();
        $self->layvka = $layvka;
        return $self;
    }

    public static function fromValue($layvka): Layvka
    {
        $self = new self();
        $self->layvka = $layvka;
        return $self;
    }

    public function toValue()
    {
        return $this->layvka;
    }

    public function isArray()
    {
        return is_array($this->layvka);
    }
}
