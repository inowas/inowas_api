<?php

namespace AppBundle\Model;

use AppBundle\Entity\AreaType;

class AreaTypeFactory
{
    private final function __construct(){}

    /**
     * @return AreaType
     */
    public static function create()
    {
        return new AreaType();
    }

    /**
     * @param string $name
     * @return AreaType
     */
    public static function setName($name = "")
    {
        $areaType = new AreaType();
        $areaType->setName($name);

        return $areaType;
    }
}