<?php

namespace Inowas\ModflowBundle\Model;


class CalculationFactory
{

    final private function __construct(){}

    public static function create(){
        return new Calculation();
    }

}