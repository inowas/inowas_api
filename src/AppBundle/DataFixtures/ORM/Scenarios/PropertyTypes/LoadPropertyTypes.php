<?php

namespace AppBundle\DataFixtures\ORM\Scenarios\PropertyTypes;

use AppBundle\Entity\PropertyType;
use AppBundle\Model\PropertyTypeFactory;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadPropertyTypes implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $entityManager)
    {
        $propertyTypesArray = array(
            array("kx", "Hydraulic Conductivity in x-Direction", PropertyType::STATIC_VALUE_ONLY),
            array("ky", "Hydraulic Conductivity in y-Direction", PropertyType::STATIC_VALUE_ONLY),
            array("kz", "Hydraulic Conductivity in z-Direction", PropertyType::STATIC_VALUE_ONLY),
            array("sy", "Specific yield", PropertyType::STATIC_VALUE_ONLY),
            array("ss", "Specific storage", PropertyType::STATIC_VALUE_ONLY),
            array("et", "Top elevation", PropertyType::STATIC_VALUE_ONLY),
            array("eb", "Bottom elevation", PropertyType::STATIC_VALUE_ONLY),
            array("hc", "Hydraulic Conductivity", PropertyType::STATIC_VALUE_ONLY),
            array("ha", "Horizontal anisotropy", PropertyType::STATIC_VALUE_ONLY),
            array("va", "Vertical anisotropy", PropertyType::STATIC_VALUE_ONLY),
            array("vc", "Vertical conductance", PropertyType::STATIC_VALUE_ONLY),
            array("hh", "Hydraulic Head", PropertyType::STATIC_AND_TIME_DEPENDENT_VALUES),
            array("gr", "Groundwater Recharge in m", PropertyType::STATIC_AND_TIME_DEPENDENT_VALUES),
            array("tmin", "Temperature minimum", PropertyType::STATIC_AND_TIME_DEPENDENT_VALUES),
            array("tmax", "Temperature maximum", PropertyType::STATIC_AND_TIME_DEPENDENT_VALUES),
            array("pr", "Precipitation in m", PropertyType::STATIC_AND_TIME_DEPENDENT_VALUES),
            array("pe", "Potential evapotranspiration in mm", PropertyType::STATIC_AND_TIME_DEPENDENT_VALUES),
            array("pur", "Pumping rate in m^/day", PropertyType::STATIC_AND_TIME_DEPENDENT_VALUES)
        );

        foreach ($propertyTypesArray as $propertyTypeArray)
        {
            $propertyType = PropertyTypeFactory::create()
                ->setAbbreviation($propertyTypeArray[0])
                ->setName($propertyTypeArray[1])
                ->setValueType($propertyTypeArray[2])
            ;

            $entityManager->persist($propertyType);
        }
        
        $entityManager->flush();
    }
}