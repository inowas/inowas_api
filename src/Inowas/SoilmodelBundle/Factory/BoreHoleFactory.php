<?php

namespace Inowas\SoilmodelBundle\Factory;

use Inowas\SoilmodelBundle\Model\BoreHole;

class BoreHoleFactory
{
    final private function __construct(){}

    public static function create(){
        return new BoreHole();
    }
}