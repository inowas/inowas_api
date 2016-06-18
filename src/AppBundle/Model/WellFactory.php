<?php

namespace AppBundle\Model;

use AppBundle\Entity\Well;

class WellFactory
{
    public static function create(){
        return new Well();
    }

    public static function createIndustrialWell(){
        $well = new Well();
        $well->setWellType(Well::TYPE_INDUSTRIAL_WELL);
        return $well;
    }

    public static function createPrivateWell(){
        $well = new Well();
        $well->setWellType(Well::TYPE_PRIVATE_WELL);
        return $well;
    }
}