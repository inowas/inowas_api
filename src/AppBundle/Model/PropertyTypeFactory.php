<?php

namespace AppBundle\Model;

class PropertyTypeFactory
{
    private final function __construct(){}

    /**
     * @param $abbreviation
     * @return PropertyType
     */
    public static function create($abbreviation)
    {
        return PropertyType::fromAbbreviation($abbreviation);
    }
}
