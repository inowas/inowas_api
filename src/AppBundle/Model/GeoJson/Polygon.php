<?php

namespace AppBundle\Model\GeoJson;

class Polygon
{
    protected $type = "Polygon";
    protected $coordinates = array();

    public function setCoordinates($coordinates)
    {
        $this->coordinates = $coordinates;
    }
}