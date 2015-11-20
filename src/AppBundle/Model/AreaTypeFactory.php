<?php

namespace AppBundle\Model;

use AppBundle\Entity\AreaType;

class AreaTypeFactory
{
    /**
     * GeologicalPointFactory constructor.
     */
    public function __construct()
    {
        return new AreaType();
    }

    public static function setName($name = "")
    {
        $areaType = new AreaType();
        $areaType->setName($name);

        return $areaType;
    }
}