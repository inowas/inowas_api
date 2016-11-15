<?php

namespace Inowas\SoilmodelBundle\Model;

use Inowas\Soilmodel\Model\BoreHole;

class BoreHoleFactory
{
    final private function __construct(){}

    public static function create(){
        return new BoreHole();
    }
}