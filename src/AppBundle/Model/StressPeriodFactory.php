<?php
/**
 * Created by PhpStorm.
 * User: Ralf
 * Date: 21.03.16
 * Time: 21:09
 */

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