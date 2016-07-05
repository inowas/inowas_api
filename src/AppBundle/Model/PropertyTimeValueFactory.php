<?php

namespace AppBundle\Model;

use AppBundle\Entity\PropertyTimeValue;

class PropertyTimeValueFactory
{
    private final function __construct(){}

    /**
     * @return PropertyTimeValue
     */
    public static function create()
    {
        return new PropertyTimeValue();
    }

    /**
     * @param \DateTime $timestamp
     * @param $value
     * @return PropertyTimeValue
     */
    public static function createWithTimeAndValue(\DateTime $timestamp, $value)
    {
        $ptv = new PropertyTimeValue();
        $ptv->setTimeStamp($timestamp);
        $ptv->setValue($value);

        return $ptv;
    }

    /**
     * @param \DateTime $dateTime
     * @return PropertyTimeValue
     */
    public static function createWithTime(\DateTime $dateTime)
    {
        $propertyTimeValue = new PropertyTimeValue();
        $propertyTimeValue->setDatetime($dateTime);
        return $propertyTimeValue;
    }
}