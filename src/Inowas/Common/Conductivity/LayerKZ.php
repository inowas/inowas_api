<?php

declare(strict_types=1);

namespace Inowas\Common\Conductivity;

class LayerKZ extends LayerConductivityValue
{
    public static function fromArray(array $values): LayerKZ
    {
        $self = new self();
        $self->values = $values;
        return $self;
    }
}
