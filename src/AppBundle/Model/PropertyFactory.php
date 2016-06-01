<?php

namespace AppBundle\Model;

use AppBundle\Entity\Property;
use AppBundle\Entity\PropertyType;

class PropertyFactory
{
    /**
     * PropertyFactory constructor.
     */
    public function __construct()
    {
        return new Property();
    }

    public static function create()
    {
        return new Property();
    }

    public static function setTypeAndModelObject(PropertyType $type)
    {
        $property = new Property();
        $property->setPropertyType($type);

        return $property;
    }
}