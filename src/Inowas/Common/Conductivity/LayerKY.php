<?php

declare(strict_types=1);

namespace Inowas\Common\Conductivity;

class LayerKY extends LayerConductivityValue
{
    public static function fromArray(array $values): LayerKY
    {
        $self = new self();
        $self->values = $values;
        return $self;
    }

    public function identifier():string
    {
        return 'ky';
    }
}
