<?php

namespace Inowas\SoilmodelBundle\Factory;

use Inowas\SoilmodelBundle\Model\Soilmodel;

class SoilmodelFactory
{
    final private function __construct(){}

    public static function create(){
        return new Soilmodel();
    }
}