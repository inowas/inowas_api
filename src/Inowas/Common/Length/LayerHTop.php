<?php

declare(strict_types=1);

namespace Inowas\Common\Length;

class LayerHTop
{
    /** @var  array */
    private $values;

    public static function fromArray(array $values)
    {
        $self = new self();
        $self->values = $values;
        return $self;
    }

    public function toArray(): array
    {
        return $this->values;
    }

    public function identifier():string
    {
        return 'hTop';
    }
}
