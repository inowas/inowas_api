<?php

namespace AppBundle\Model;

class PointFactory
{
    private final function __construct(){}

    public static function fromLatLng($latLng){
        $point = new Point($latLng->lng, $latLng->lat, 4326);
        return $point;
    }
}