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

    public static function fromValue($values): LayerKX
    {
        $self = new self();
        $self->values = $values;
        return $self;
    }

    public function identifier():string
    {
        return 'kx';
    }
}
