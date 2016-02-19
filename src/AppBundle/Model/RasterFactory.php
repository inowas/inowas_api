<?php

namespace AppBundle\Model;


use AppBundle\Entity\Raster;

class RasterFactory
{
    public static function create()
    {
        return new Raster();
    }
}