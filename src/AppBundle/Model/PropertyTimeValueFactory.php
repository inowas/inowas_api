<?php

namespace AppBundle\Model;

use AppBundle\Entity\PropertyTimeValue;

class PropertyTimeValueFactory
{
    private final function __construct(){}

    /**
     * @param \DateTime $timestamp
     * @param $value
     * @return PropertyTimeValue
     */
    public static function setDateTimeAndValue(\DateTime $timestamp, $value)
    {
        $ptv = new PropertyTimeValue();
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