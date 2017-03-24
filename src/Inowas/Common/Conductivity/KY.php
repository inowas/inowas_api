<?php

namespace Inowas\Common\Conductivity;

class KY extends ConductivityValue
{

    public static function fromMPerDay(float $value){
        $self = new self();
        $self->mPerDay = $value;
        return $self;
    }

}
