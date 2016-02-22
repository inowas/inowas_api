<?php

namespace AppBundle\Model;

use AppBundle\Entity\Raster as RasterEntity;

class RasterFactory
{
    public static function createModel()
    {
        return new Raster();
    }

    public static function createEntity()
    {
        return new RasterEntity();
    }
}