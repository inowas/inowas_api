<?php

namespace Inowas\ModflowBundle\Model\Boundary;

class ObservationPointFactory
{
    public static function create(){
        return new ObservationPoint();
    }

}