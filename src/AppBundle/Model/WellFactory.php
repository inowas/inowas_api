<?php

namespace AppBundle\Model;

use AppBundle\Entity\IndustrialWell;
use AppBundle\Entity\PrivateWell;
use AppBundle\Entity\Well;

class WellFactory
{
    public static function create(){
        return new Well();
    }

    public static function createIndustrialWell(){
        return new IndustrialWell();
    }

    public static function createPrivateWell(){
        return new PrivateWell();
    }
}