<?php

namespace Inowas\SoilmodelBundle\Factory;

use Inowas\SoilmodelBundle\Model\Layer;

class LayerFactory
{
    private function __construct(){}

    public static function create(){
        return new Layer();
    }
}