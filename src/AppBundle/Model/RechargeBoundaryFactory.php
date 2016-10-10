<?php

namespace AppBundle\Model;

use AppBundle\Entity\RechargeBoundary;

class RechargeBoundaryFactory
{

    private final function __construct(){}

    /**
     * @return RechargeBoundary
     */
    public static function create(){
        return new RechargeBoundary();
    }
}
