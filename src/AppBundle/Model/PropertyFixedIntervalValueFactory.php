<?php

namespace AppBundle\Model;

use AppBundle\Entity\PropertyFixedIntervalValue;

class PropertyFixedIntervalValueFactory
{

    private final function __construct(){}

    /**
     * @return PropertyFixedIntervalValue
     */
    public static function create()
    {
        return new PropertyFixedIntervalValue();
    }
}
