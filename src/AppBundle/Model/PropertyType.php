<?php

namespace AppBundle\Model;

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


    /**
     * @var
     * @JMS\Groups({"list", "details", "modeldetails", "modelobjectdetails", "soilmodeldetails", "soilmodellayers"})
     */
    protected $abbreviation;

    /**
     * @var
     * @JMS\Groups({"list", "details", "modeldetails", "modelobjectdetails", "soilmodeldetails", "soilmodellayers"})
     */
    protected $description;

    /**
     * @var int
     * @JMS\Groups({"list", "details", "modeldetails", "modelobjectdetails", "soilmodeldetails", "soilmodellayers"})
     */
    protected $valueType;

    /**
     * PropertyType constructor.
     * @param $abbreviation
     * @param $description
     * @param $valueType
     */
    public function __construct($abbreviation, $description, $valueType)
    {
        $this->abbreviation = $abbreviation;
        $this->description = $description;
        $this->valueType = self::STATIC_AND_TIME_DEPENDENT_VALUES;
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
