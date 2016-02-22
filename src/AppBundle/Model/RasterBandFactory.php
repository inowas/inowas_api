<?php

namespace AppBundle\Model;

class RasterBandFactory
{
    public static function create()
    {
        return new RasterBand();
    }
}