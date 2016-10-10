<?php

namespace AppBundle\Model;

use AppBundle\Entity\Raster;

class RasterFactory
{

    private final function __construct(){}

    /**
     * @return Raster
     */
    public static function create()
    {
        return new Raster();
    }
}
