<?php

namespace Inowas\ModflowBundle\Model;

class AreaFactory
{
    final private function __construct(){}

    /**
     * @return Area
     */
    public static function create(){
        return new Area();
    }
}