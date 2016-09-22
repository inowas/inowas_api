<?php

namespace AppBundle\Model;

use AppBundle\Entity\GeologicalUnit;

class GeologicalUnitFactory
{

    private final function __construct(){}

    /**
     * @return GeologicalUnit
     */
    public static function create()
    {
        return new GeologicalUnit();
    }
}
