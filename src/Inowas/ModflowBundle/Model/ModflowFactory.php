<?php

namespace Inowas\ModflowBundle\Model;

class ModflowFactory
{
    final private function __construct(){}

    public static function create(){
        return new Modflow();
    }
}