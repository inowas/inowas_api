<?php

namespace AppBundle\Model;

use AppBundle\Entity\Property;
use AppBundle\Entity\PropertyTimeValue;

class PropertyTimeValueFactory
{
    /**
     * PropertyTimeValueFactory constructor.
     */
    public function __construct()
    {
        return new PropertyTimeValue();
    }

    public static function setPropertyDateTimeAndValue(Property $property, \DateTime $timestamp, $value)
    {
        $ptv = new PropertyTimeValue();
        $ptv->setProperty($property);
        $ptv->setTimeStamp($timestamp);
        $ptv->setValue($value);

        return $ptv;
    }
}