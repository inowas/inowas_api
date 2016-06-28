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

    /**
     * @param string $name
     * @return PropertyType
     */
    public static function setName($name = "")
    {
        $propertyType = new PropertyType();
        $propertyType->setName($name);

        return $propertyType;
    }
}