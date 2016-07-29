<?php

namespace AppBundle\Model;

use AppBundle\Entity\Property;

class PropertyFactory
{

    private final function __construct(){}

    /**
     * @return Property
     */
    public static function create()
    {
        return new Property();
    }

    /**
     * @param PropertyType $type
     * @return Property
     */
    public static function createWithType(PropertyType $type)
    {
        $property = new Property();
        $property->setPropertyType($type);

        return $property;
    }
}