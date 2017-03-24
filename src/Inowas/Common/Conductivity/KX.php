<?php

namespace Inowas\Common\Conductivity;

class KX extends ConductivityValue
{

    public static function fromMPerDay(float $value){
        $self = new self();
        $self->mPerDay = $value;
        return $self;
    }

}
