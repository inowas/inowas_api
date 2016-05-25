<?php

namespace AppBundle\Model;

use AppBundle\Entity\PropertyType;
use AppBundle\Entity\Project;
use FOS\UserBundle\Model\UserInterface;

class PropertyTypeFactory
{
    /**
     * GeologicalPointFactory constructor.
     */
    public function __construct()
    {
        return new PropertyType();
    }

    public static function create()
    {
        return new PropertyType();
    }

    public static function setName($name = "")
    {
        $propertyType = new PropertyType();
        $propertyType->setName($name);

        return $propertyType;
    }
}