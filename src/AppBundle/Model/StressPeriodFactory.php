<?php

namespace AppBundle\Model;


class StressPeriodFactory
{

    private final function __construct(){}

    /**
     * @return StressPeriod
     */
    public static function create()
    {
        return new StressPeriod();
    }
}