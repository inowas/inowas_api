<?php

namespace Inowas\Common\Storage;

class LayerSpecificYield
{

    /** @var array */
    protected $values;

    public static function fromArray(array $values){
        $self = new self();
        $self->values = $values;
        return $self;
    }

    public function toArray(){
        return $this->values;
    }

    public function identifier():string
    {
        return 'sy';
    }
}
