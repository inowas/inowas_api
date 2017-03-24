<?php

namespace Inowas\Common\Length;

class HTop extends Height
{
    private $heightInMillimeters;

    public static function fromMeters(float $height)
    {
        $self = new self();
        $self->heightInMillimeters = intval($height*1000);
        return $self;
    }

    public function toMeters(): float
    {
        return floatval($this->heightInMillimeters/1000);
    }
}
