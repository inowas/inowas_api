<?php

namespace AppBundle\Model;

use AppBundle\Entity\SoilModel;

class SoilModelFactory
{

    private final function __construct(){}

    /**
     * @return SoilModel
     */
    public static function create()
    {
        $soilModel = new SoilModel();

        return $soilModel;
    }
}
