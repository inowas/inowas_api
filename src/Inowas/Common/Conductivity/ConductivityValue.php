<?php

namespace Inowas\Common\Conductivity;

class ConductivityValue
{

    /** @var  float */
    protected $mPerDay;

    public static function fromMPerDay(float $value){
        $self = new self();
        $self->mPerDay = $value;
        return $self;
    }

    public function mPerDay(){
        return $this->mPerDay;
    }
}
