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
}

