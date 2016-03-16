<?php

namespace AppBundle\Model;

use AppBundle\Entity\SoilModel;

class SoilModelFactory
{
    public static function create()
    {
        $soilModel = new SoilModel();

        return $soilModel;
    }
}