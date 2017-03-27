<?php

declare(strict_types=1);

namespace Inowas\Common\Conductivity;

class LayerKX extends LayerConductivityValue
{
    public static function fromArray(array $values): LayerKX
    {
        $self = new self();
        $self->values = $values;
        return $self;
    }
}
