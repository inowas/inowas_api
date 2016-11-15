<?php

namespace Inowas\Soilmodel\Model;

class SoilmodelFactory
{
    final private function __construct(){}

    public static function create(){
        return new Soilmodel();
    }
}