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

    public static function create()
    {
        return new StressPeriod();
    }



}