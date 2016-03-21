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
            array("hh", "Hydraulic Head"),
            array("kx", "Hydraulic Conductivity in x-Direction"),
            array("ky", "Hydraulic Conductivity in y-Direction"),
            array("kz", "Hydraulic Conductivity in z-Direction"),
            array("sy", "Specific yield"),
            array("ss", "Specific storage"),
            array("et", "Top elevation"),
            array("eb", "Bottom elevation"),
            array("hc", "Hydraulic Conductivity"),
            array("ha", "Horizontal anisotropy"),
            array("va", "Vertical anisotropy"),
            array("gr", "Groundwater Recharge in m"),
            array("tmin", "Temperature minimum"),
            array("tmax", "Temperature maximum"),
            array("pr", "Precipitation in m"),
            array("pe", "Potential evapotranspiration in mm"),
            array("vc", "Vertical conductance")
        );

        foreach ($propertyTypesArray as $propertyTypeArray)
        {
            $propertyType = PropertyTypeFactory::setName($propertyTypeArray[1]);
            $propertyType->setAbbreviation($propertyTypeArray[0]);
            $entityManager->persist($propertyType);
            $entityManager->flush();
        }
    }
}