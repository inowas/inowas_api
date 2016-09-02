<?php

namespace AppBundle\Model;

use CrEOF\Spatial\PHP\Types\Geometry\LineString;

class LineStringFactory
{

    private final function __construct(){}

    public static function fromLatLngs($latLngs){
        $points = array();

        foreach ($latLngs as $latLng){
            array_push($points, array($latLng->lng, $latLng->lat));
        }

        return new LineString($points, 4326);
    }

}