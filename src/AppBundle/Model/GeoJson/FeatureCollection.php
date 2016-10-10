<?php

namespace AppBundle\Model\GeoJson;


class FeatureCollection
{

    protected $type = 'FeatureCollection';
    
    protected $features;
    
    public function __construct()
    {
        $this->features = array();
    }

    public function addFeature(Feature $feature)
    {
        $this->features[] = $feature;
    }
}
