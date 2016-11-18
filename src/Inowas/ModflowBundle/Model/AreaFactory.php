<?php

namespace Inowas\ModflowBundle\Model;

class AreaFactory
{
    final private function __construct(){}

    public static function create(){
        return new Area();
    }
}