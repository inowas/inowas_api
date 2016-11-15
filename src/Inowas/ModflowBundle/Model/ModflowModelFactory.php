<?php

namespace Inowas\ModflowBundle\Model;

class ModflowModelFactory
{
    final private function __construct(){}

    public static function create(){
        return new ModFlowModel();
    }
}