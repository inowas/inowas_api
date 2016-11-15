<?php

namespace Inowas\Soilmodel\Factory;

use Inowas\Soilmodel\Model\Soilmodel;

class SoilmodelFactory
{
    final private function __construct(){}

    public static function create(){
        return new Soilmodel();
    }
}