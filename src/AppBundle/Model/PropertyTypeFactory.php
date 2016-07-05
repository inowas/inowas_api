<?php

namespace AppBundle\Model;

use AppBundle\Entity\PropertyType;

class PropertyTypeFactory
{

    private final function __construct(){}

    /**
     * @return PropertyType
     */
    public static function create()
    {
        return new PropertyType();
    }
}