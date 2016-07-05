<?php

namespace AppBundle\Model;

use AppBundle\Entity\ObservationPoint;

class ObservationPointFactory
{

    private final function __construct(){}

    /**
     * @return ObservationPoint
     */
    public static function create()
    {
        return new ObservationPoint();
    }
}