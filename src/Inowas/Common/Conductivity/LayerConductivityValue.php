<?php

namespace Inowas\Common\Conductivity;

class LayerConductivityValue
{

    /** @var array */
    protected $values;

    public static function fromArray(array $values)
    {
        $self = new self();
        $self->values = $values;
        return $self;
    }

    public static function fromValue($values)
    {
        $self = new self();
        $self->values = $values;
        return $self;
    }

    public function toArray(): array
    {
        return $this->values;
    }

    public function toValue()
    {
        return $this->values;
    }
}
