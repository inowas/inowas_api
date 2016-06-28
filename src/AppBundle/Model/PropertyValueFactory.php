<?php

namespace AppBundle\Model;

use AppBundle\Entity\PropertyValue;

class PropertyValueFactory
{

    private final function __construct(){}

    /**
     * @return PropertyValue
     */
    public static function create()
    {
        return new PropertyValue();
    }
}