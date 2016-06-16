<?php

namespace AppBundle\Model\GeoJson;

class Feature
{
    protected $type = "Feature";

    protected $id;

    protected $properties;

    protected $geometry;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function setProperties($properties)
    {
        $this->properties = $properties;
    }

    public function setGeometry($geometry)
    {
        $this->geometry = $geometry;
    }

}