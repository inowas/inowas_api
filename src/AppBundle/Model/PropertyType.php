<?php

namespace AppBundle\Model;

use AppBundle\Exception\InvalidArgumentException;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Class PropertyType
 * @package AppBundle\Model
 */
class PropertyType
{
    const STATIC_VALUE_ONLY = 1;
    const TIME_DEPENDENT_VALUE_ONLY = 2;
    const STATIC_AND_TIME_DEPENDENT_VALUES = 3;

    const KX = 'kx';
    const KY = 'ky';
    const KZ = 'kz';
    const SPECIFIC_STORAGE = 'ss';
    const SPECIFIC_YIELD = 'sy';
    const TOP_ELEVATION = 'et';
    const BOTTOM_ELEVATION = 'eb';
    const HYDRAULIC_CONDUCTIVITY = 'hc';
    const HORIZONTAL_ANISOTROPY = 'ha';
    const VERTICAL_ANISOTROPY = 'va';
    const VERTICAL_CONDUCTANCE = 'vc';
    const HYDRAULIC_HEAD = 'hh';
    const RECHARGE = 're';
    const TEMPERATURE_MIN = 'tmin';
    const TEMPERATURE_MAX = 'tmax';
    const PRECIPITATION = 'pr';
    const POTENCIAL_EVAPOTRANSPIRATION = 'pe';
    const PUMPING_RATE = 'pur';
    const RIVER_STAGE = 'rs';
    const RIVERBED_CONDUCTANCE = 'rbc';

    private $availablePropertyTypes = array(
        self::KX => array("Hydraulic Conductivity in x-Direction", self::STATIC_VALUE_ONLY),
        self::KY => array("Hydraulic Conductivity in y-Direction", self::STATIC_VALUE_ONLY),
        self::KZ => array("Hydraulic Conductivity in z-Direction", self::STATIC_VALUE_ONLY),
        self::SPECIFIC_YIELD => array("Specific yield", self::STATIC_VALUE_ONLY),
        self::SPECIFIC_STORAGE => array("Specific storage", self::STATIC_VALUE_ONLY),
        self::TOP_ELEVATION => array("Top elevation", self::STATIC_VALUE_ONLY),
        self::BOTTOM_ELEVATION => array("Bottom elevation", self::STATIC_VALUE_ONLY),
        self::HYDRAULIC_CONDUCTIVITY => array("Hydraulic Conductivity", self::STATIC_VALUE_ONLY),
        self::HORIZONTAL_ANISOTROPY => array("Horizontal anisotropy", self::STATIC_VALUE_ONLY),
        self::VERTICAL_ANISOTROPY => array("Vertical anisotropy", self::STATIC_VALUE_ONLY),
        self::VERTICAL_CONDUCTANCE => array("Vertical conductance", self::STATIC_VALUE_ONLY),
        self::HYDRAULIC_HEAD => array("Hydraulic Head", self::STATIC_AND_TIME_DEPENDENT_VALUES),
        self::RECHARGE => array("Groundwater Recharge in m", self::STATIC_AND_TIME_DEPENDENT_VALUES),
        self::TEMPERATURE_MIN => array("Temperature minimum", self::STATIC_AND_TIME_DEPENDENT_VALUES),
        self::TEMPERATURE_MAX => array("Temperature maximum", self::STATIC_AND_TIME_DEPENDENT_VALUES),
        self::PRECIPITATION => array("Precipitation in m", self::STATIC_AND_TIME_DEPENDENT_VALUES),
        self::POTENCIAL_EVAPOTRANSPIRATION => array("Potential evapotranspiration in mm", self::STATIC_AND_TIME_DEPENDENT_VALUES),
        self::PUMPING_RATE => array("Pumping rate in m^/day", self::STATIC_AND_TIME_DEPENDENT_VALUES),
        self::RIVER_STAGE => array("River stage", self::STATIC_AND_TIME_DEPENDENT_VALUES),
        self::RIVERBED_CONDUCTANCE => array("Riverbed Conductance", self::STATIC_AND_TIME_DEPENDENT_VALUES)
    );

    /**
     * @var
     * @JMS\Groups({"list", "details", "modeldetails", "modelobjectdetails", "soilmodeldetails", "soilmodellayers"})
     */
    protected $abbreviation;

    /**
     * @var
     * @JMS\Groups({"list", "details", "modeldetails", "modelobjectdetails", "soilmodeldetails", "soilmodellayers"})
     */
    protected $description = "";

    /**
     * @var int
     * @JMS\Groups({"list", "details", "modeldetails", "modelobjectdetails", "soilmodeldetails", "soilmodellayers"})
     */
    protected $valueType = self::STATIC_AND_TIME_DEPENDENT_VALUES;

    /**
     * PropertyType constructor.
     */
    private final function __construct()
    {}

    /**
     * @param $abbreviation
     * @return PropertyType
     */
    public static function fromAbbreviation($abbreviation){

        $instance = new self();

        if (! array_key_exists($abbreviation, $instance->availablePropertyTypes)){
            throw new InvalidArgumentException(sprintf('PropertyType with Abbreviation %s is unknown', $abbreviation));
        }

        $instance->abbreviation = $abbreviation;

        if (array_key_exists($abbreviation, $instance->availablePropertyTypes)){
            $instance->description = $instance->availablePropertyTypes[$abbreviation][0];
            $instance->valueType = $instance->availablePropertyTypes[$abbreviation][1];
            return $instance;
        }
    }

    /**
     * Get abbreviation
     *
     * @return string
     */
    public function getAbbreviation()
    {
        return $this->abbreviation;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return int
     */
    public function getValueType()
    {
        return $this->valueType;
    }
}
