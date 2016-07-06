<?php

namespace AppBundle\Model;

use AppBundle\Entity\WellBoundary;

class WellBoundaryFactory
{

    private final function __construct(){}

    /**
     * @return WellBoundary
     */
    public static function create(){
        return new WellBoundary();
    }

    /**
     * @return WellBoundary
     */
    public static function createIndustrialWell(){
        $well = new WellBoundary();
        $well->setWellType(WellBoundary::TYPE_INDUSTRIAL_WELL);
        return $well;
    }

    /**
     * @return WellBoundary
     */
    public static function createPrivateWell(){
        $well = new WellBoundary();
        $well->setWellType(WellBoundary::TYPE_PRIVATE_WELL);
        return $well;
    }
}