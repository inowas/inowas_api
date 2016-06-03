<?php

namespace AppBundle\Model;

use AppBundle\Entity\PropertyValue;

class PropertyValueFactory
{
    /**
     * PropertyTimeValueFactory constructor.
     */
    public function __construct()
    {
        return new PropertyValue();
    }

    public static function create()
    {
        return new PropertyValue();
    }
}