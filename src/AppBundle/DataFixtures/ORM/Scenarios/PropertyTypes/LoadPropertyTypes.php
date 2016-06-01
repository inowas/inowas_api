<?php

namespace AppBundle\DataFixtures\ORM\Scenarios\PropertyTypes;

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
            array("kx", "Hydraulic Conductivity in x-Direction", true, false),
            array("ky", "Hydraulic Conductivity in y-Direction", true, false),
            array("kz", "Hydraulic Conductivity in z-Direction", true, false),
            array("sy", "Specific yield", true, false),
            array("ss", "Specific storage", true, false),
            array("et", "Top elevation", true, false),
            array("eb", "Bottom elevation", true, false),
            array("hc", "Hydraulic Conductivity", true, false),
            array("ha", "Horizontal anisotropy", true, false),
            array("va", "Vertical anisotropy", true, false),
            array("vc", "Vertical conductance", true, false),
            array("hh", "Hydraulic Head", true, true),
            array("gr", "Groundwater Recharge in m", true, true),
            array("tmin", "Temperature minimum", true, true),
            array("tmax", "Temperature maximum", true, true),
            array("pr", "Precipitation in m", true, true),
            array("pe", "Potential evapotranspiration in mm", true, true),
        );

        foreach ($propertyTypesArray as $propertyTypeArray)
        {
            $propertyType = PropertyTypeFactory::create()
                ->setAbbreviation($propertyTypesArray[0])
                ->setName($propertyTypeArray[1])
                ->setCanBeStatic($propertyTypeArray[2])
                ->setCanBeTimeDependent($propertyTypeArray[3])
            ;

            $entityManager->persist($propertyType);
        }
        
        $entityManager->flush();
    }
}