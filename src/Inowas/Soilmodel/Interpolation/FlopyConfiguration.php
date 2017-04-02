<?php

namespace Inowas\Soilmodel\Interpolation;

class FlopyConfiguration implements \JsonSerializable
{

    /** @var  array */
    private $data;

    public static function fromData(array $data){
        $self = new self();
        $self->data = $data;
        return $self;
    }

    public function jsonSerialize() {
        return $this->data;
    }
}
