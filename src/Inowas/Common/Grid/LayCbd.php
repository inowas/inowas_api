<?php
/**
 * laycbd : int or array of ints (nlay), optional
 * An array of flags indicating whether or not a layer has a Quasi-3D
 * confining bed below it. 0 indicates no confining bed, and not zero
 * indicates a confining bed. LAYCBD for the bottom layer must be 0.
 * (the default is 0)
 */
declare(strict_types=1);

namespace Inowas\Common\Grid;

class LayCbd
{
    protected $layCbd;

    public static function fromArray(array $layCbd): LayCbd
    {
        $self = new self();
        $arr = [];
        foreach ($layCbd as $element){
            $arr[] = (int)$element;
        }
        $self->layCbd = $arr;
        return $self;
    }

    public static function fromValue($layCbd): LayCbd
    {
        $self = new self();
        $self->layCbd = $layCbd;
        return $self;
    }

    public function toValue()
    {
        return $this->layCbd;
    }

    public function isArray()
    {
        return is_array($this->layCbd);
    }
}
