<?php

namespace AppBundle\Model;

class TimeValueFactory
{

    private final function __construct(){}

    /**
     * @return TimeValue
     */
    public static function create()
    {
        return new TimeValue();
    }

    /**
     * @param $value
     * @return TimeValue
     */
    public static function setValue($value)
    {
        $timeValue = new TimeValue();
        $timeValue->setValue($value);
        return $timeValue;
    }

    /**
     * @param \DateTime $dateTime
     * @param $value
     * @return TimeValue
     */
    public static function setDateTimeAndValue(\DateTime $dateTime, $value)
    {
        $timeValue = new TimeValue();
        $timeValue->setDatetime($dateTime);
        $timeValue->setValue($value);
        return $timeValue;
    }
}
