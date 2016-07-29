<?php

namespace AppBundle\Model;

use AppBundle\Exception\InvalidArgumentException;

class PropertyTypeFactory
{

    public static $availablePropertyTypes = array(
        PropertyType::KX => array("Hydraulic Conductivity in x-Direction", PropertyType::STATIC_VALUE_ONLY),
        PropertyType::KY => array("Hydraulic Conductivity in y-Direction", PropertyType::STATIC_VALUE_ONLY),
        PropertyType::KZ => array("Hydraulic Conductivity in z-Direction", PropertyType::STATIC_VALUE_ONLY),
        PropertyType::SPECIFIC_YIELD => array("Specific yield", PropertyType::STATIC_VALUE_ONLY),
        PropertyType::SPECIFIC_STORAGE => array("Specific storage", PropertyType::STATIC_VALUE_ONLY),
        PropertyType::TOP_ELEVATION => array("Top elevation", PropertyType::STATIC_VALUE_ONLY),
        PropertyType::BOTTOM_ELEVATION => array("Bottom elevation", PropertyType::STATIC_VALUE_ONLY),
        PropertyType::HYDRAULIC_CONDUCTIVITY => array("Hydraulic Conductivity", PropertyType::STATIC_VALUE_ONLY),
        PropertyType::HORIZONTAL_ANISOTROPY => array("Horizontal anisotropy", PropertyType::STATIC_VALUE_ONLY),
        PropertyType::VERTICAL_ANISOTROPY => array("Vertical anisotropy", PropertyType::STATIC_VALUE_ONLY),
        PropertyType::VERTICAL_CONDUCTANCE => array("Vertical conductance", PropertyType::STATIC_VALUE_ONLY),
        PropertyType::HYDRAULIC_HEAD => array("Hydraulic Head", PropertyType::STATIC_AND_TIME_DEPENDENT_VALUES),
        PropertyType::RECHARGE => array("Groundwater Recharge in m", PropertyType::STATIC_AND_TIME_DEPENDENT_VALUES),
        PropertyType::TEMPERATURE_MIN => array("Temperature minimum", PropertyType::STATIC_AND_TIME_DEPENDENT_VALUES),
        PropertyType::TEMPERATURE_MAX => array("Temperature maximum", PropertyType::STATIC_AND_TIME_DEPENDENT_VALUES),
        PropertyType::PRECIPITATION => array("Precipitation in m", PropertyType::STATIC_AND_TIME_DEPENDENT_VALUES),
        PropertyType::POTENCIAL_EVAPOTRANSPIRATION => array("Potential evapotranspiration in mm", PropertyType::STATIC_AND_TIME_DEPENDENT_VALUES),
        PropertyType::PUMPING_RATE => array("Pumping rate in m^/day", PropertyType::STATIC_AND_TIME_DEPENDENT_VALUES),
        PropertyType::RIVER_STAGE => array("River stage", PropertyType::STATIC_AND_TIME_DEPENDENT_VALUES),
        PropertyType::RIVERBED_CONDUCTANCE => array("Riverbed Conductance", PropertyType::STATIC_AND_TIME_DEPENDENT_VALUES)
    );

    private final function __construct(){}

    /**
     * @param $abbreviation
     * @return PropertyType
     */
    public static function create($abbreviation)
    {
        if (! array_key_exists($abbreviation, self::$availablePropertyTypes)){
            throw new InvalidArgumentException(sprintf('PropertyType with Abbreviation %s is unknown', $abbreviation));
        }

        $prop = self::$availablePropertyTypes[$abbreviation];
        return new PropertyType($abbreviation, $prop[0], $prop[1]);
    }
}