<?php

namespace Inowas\Soilmodel\Factory;

use Inowas\Soilmodel\Model\BoreHole;

class BoreHoleFactory
{
    final private function __construct(){}

    public static function create(){
        return new BoreHole();
    }
}