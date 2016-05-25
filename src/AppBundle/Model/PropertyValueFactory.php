<?php

namespace AppBundle\Model;

use AppBundle\Entity\Property;
use AppBundle\Entity\PropertyValue;

class PropertyValueFactory
{
    /**
     * PropertyTimeValueFactory constructor.
     */
    public function __construct()
    {
        return new PropertyValue();
    }

    public static function setPropertyAndValue(Property $property, $value)
    {
        $pv = new PropertyValue();
        $pv->setProperty($property);
        $pv->setValue($value);

        return $pv;
    }

    public static function create()
    {
        return new PropertyValue();
    }
}