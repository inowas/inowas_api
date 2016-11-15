<?php

namespace Inowas\SoilmodelBundle\Model;

use Inowas\Soilmodel\Model\Layer;

class LayerFactory
{
    private function __construct(){}

    public static function create(){
        return new Layer();
    }
}