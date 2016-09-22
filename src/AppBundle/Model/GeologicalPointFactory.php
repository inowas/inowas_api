<?php

namespace AppBundle\Model;

use AppBundle\Entity\GeologicalPoint;

class GeologicalPointFactory
{

    private final function __construct(){}

    /**
     * @return GeologicalPoint
     */
    public static function create()
    {
        return new GeologicalPoint();
    }
}
