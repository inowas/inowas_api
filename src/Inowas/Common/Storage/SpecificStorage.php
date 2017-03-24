<?php

namespace Inowas\Common\Storage;

class SpecificStorage
{

    /** @var  float */
    protected $value;

    public static function fromFloat(float $value){
        $self = new self();
        $self->value = $value;
        return $self;
    }

    public function toFloat(){
        return $this->value;
    }
}
