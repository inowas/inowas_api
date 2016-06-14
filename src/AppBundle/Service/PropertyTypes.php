<?php

namespace AppBundle\Service;

use AppBundle\Entity\PropertyType;
use AppBundle\Model\PropertyTypeFactory;
use Doctrine\Common\Collections\ArrayCollection;

class PropertyTypes
{
    const STATIC_VALUE_ONLY = 1;
    const TIME_DEPENDENT_VALUE_ONLY = 2;
    const STATIC_AND_TIME_DEPENDENT_VALUES = 3;

    const TOP_ELEVATION = 'et';
    const BOTTOM_ELEVATION = 'eb';

    /** @var  ArrayCollection $propertyTypes */
    protected $propertyTypes;

    protected $propertyTypesArray = array(
        array("kx", "Hydraulic Conductivity in x-Direction", self::STATIC_VALUE_ONLY),
        array("ky", "Hydraulic Conductivity in y-Direction", self::STATIC_VALUE_ONLY),
        array("kz", "Hydraulic Conductivity in z-Direction", self::STATIC_VALUE_ONLY),
        array("sy", "Specific yield", self::STATIC_VALUE_ONLY),
        array("ss", "Specific storage", self::STATIC_VALUE_ONLY),
        array("et", "Top elevation", self::STATIC_VALUE_ONLY),
        array("eb", "Bottom elevation", self::STATIC_VALUE_ONLY),
        array("hc", "Hydraulic Conductivity", self::STATIC_VALUE_ONLY),
        array("ha", "Horizontal anisotropy", self::STATIC_VALUE_ONLY),
        array("va", "Vertical anisotropy", self::STATIC_VALUE_ONLY),
        array("vc", "Vertical conductance", self::STATIC_VALUE_ONLY),
        array("hh", "Hydraulic Head", self::STATIC_AND_TIME_DEPENDENT_VALUES),
        array("gr", "Groundwater Recharge in m", self::STATIC_AND_TIME_DEPENDENT_VALUES),
        array("tmin", "Temperature minimum", self::STATIC_AND_TIME_DEPENDENT_VALUES),
        array("tmax", "Temperature maximum", self::STATIC_AND_TIME_DEPENDENT_VALUES),
        array("pr", "Precipitation in m", self::STATIC_AND_TIME_DEPENDENT_VALUES),
        array("pe", "Potential evapotranspiration in mm", self::STATIC_AND_TIME_DEPENDENT_VALUES),
        array("pur", "Pumping rate in m^/day", self::STATIC_AND_TIME_DEPENDENT_VALUES)
    );

    /**
     * PropertyTypes constructor.
     */
    public function __construct(){
        $this->propertyTypes = new ArrayCollection();

        foreach ($this->propertyTypesArray as $propertyTypeArray) {
            $propertyType = PropertyTypeFactory::create()
                ->setAbbreviation($propertyTypeArray[0])
                ->setName($propertyTypeArray[1])
                ->setValueType($propertyTypeArray[2]);

            $this->propertyTypes->add($propertyType);
        }
    }

    public function findOneByAbbreviation($abbreviation){
        /** @var PropertyType $propertyType */
        foreach ($this->propertyTypes as $propertyType) {
            if ($propertyType->getAbbreviation() == $abbreviation) {
                return $propertyType;
            }
        }
        return null;
    }
}