<?php

namespace AppBundle\Model;

class PointFactory
{
    private final function __construct(){}

    public static function fromLatLng(LatLng $latLng){
        $point = new Point($latLng->getLng(), $latLng->getLng(), 4326);
        return $point;
    }
}
