<?php

namespace AppBundle\Model;


use AppBundle\Entity\Calculation;

class CalculationFactory
{
    public static function create()
    {
        return new Calculation();
    }
}