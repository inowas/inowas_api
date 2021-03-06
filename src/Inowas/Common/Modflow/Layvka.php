<?php
/**
 * layvka : float or array of floats (nlay)
 * a flag for each layer that indicates whether variable VKA is vertical
 * hydraulic conductivity or the ratio of horizontal to vertical
 * hydraulic conductivity.
 *
 * 0—indicates VKA is vertical hydraulic conductivity
 * not 0—indicates VKA is the ratio of horizontal to vertical hydraulic conductivity,
 * where the horizontal hydraulic conductivity is specified as HK in item 10.
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

    public function isArray(): bool
    {
        return \is_array($this->layvka);
    }
}
