<?php

declare(strict_types=1);

namespace Inowas\Common\Soilmodel;

class HTop
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

    public function toValue(): float
    {
        return floatval($this->heightInMillimeters/1000);
    }
}
