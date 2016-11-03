<?php

namespace AppBundle\Model;

use CrEOF\Spatial\PHP\Types\Geometry\Polygon;

class PolygonFactory
{

    private final function __construct(){}

    public static function fromLatLngs($latLngs){
        $points = array();

        foreach ($latLngs as $latLng){
            array_push($points, array($latLng->lng, $latLng->lat));
        }

        if ($points[0] != $points[count($points)-1]){
            $points[] = $points[0];
        }
        $points = array($points);

        return new Polygon($points, 4326);
    }

}
