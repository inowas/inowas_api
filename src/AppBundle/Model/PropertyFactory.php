<?php

namespace AppBundle\Model;

use AppBundle\Entity\ModelObject;
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

    public static function setTypeAndModelObject(PropertyType $type, ModelObject $modelObject)
    {
        $property = new Property();
        $property->setPropertyType($type);
        $property->setModelObject($modelObject);

        return $property;
    }
}