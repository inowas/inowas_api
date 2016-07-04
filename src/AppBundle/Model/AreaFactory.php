<?php

namespace AppBundle\Model;

use AppBundle\Entity\Area;

class AreaFactory
{
    private final function __construct(){}

    /**
     * @return Area
     */
    public static function create()
    {
        return new Area();
    }

}