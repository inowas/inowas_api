<?php

namespace AppBundle\Model;

use AppBundle\Entity\GeologicalLayer;
use FOS\UserBundle\Model\UserInterface;

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