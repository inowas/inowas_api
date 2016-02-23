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

    /**
     * @param Property $property
     * @param \DateTime $timestamp
     * @param $value
     * @return PropertyTimeValue
     */
    public static function setPropertyDateTimeAndValue(Property $property, \DateTime $timestamp, $value)
    {
        $ptv = new PropertyTimeValue();
        $ptv->setProperty($property);
        $ptv->setTimeStamp($timestamp);
        $ptv->setValue($value);

        return $ptv;
    }

    /**
     * @return PropertyTimeValue
     */
    public static function create()
    {
        return new PropertyTimeValue();
    }

    /**
     * @return PropertyTimeValue
     */
    public static function createWithTime(\DateTime $dateTime)
    {
        $propertyTimeValue = new PropertyTimeValue();
        $propertyTimeValue->setDatetime($dateTime);
        return $propertyTimeValue;
    }
}