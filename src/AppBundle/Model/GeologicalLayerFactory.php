<?php

namespace AppBundle\Model;

use AppBundle\Entity\GeologicalLayer;

class GeologicalLayerFactory
{

    private final function __construct(){}

    /**
     * @return GeologicalLayer
     */
    public static function create()
    {
        return new GeologicalLayer();
    }
}
