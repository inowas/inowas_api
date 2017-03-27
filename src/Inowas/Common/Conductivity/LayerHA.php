<?php

declare(strict_types=1);

namespace Inowas\Common\Conductivity;

class LayerHA extends LayerConductivityValue
{
    public static function fromArray(array $values): LayerHA
    {
        $self = new self();
        $self->values = $values;
        return $self;
    }

    public function identifier():string
    {
        return 'ha';
    }
}
