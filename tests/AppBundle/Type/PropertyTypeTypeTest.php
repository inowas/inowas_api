<?php

namespace Tests\AppBundle\Type;

use AppBundle\Entity\Property;
use AppBundle\Model\PropertyFactory;
use AppBundle\Model\PropertyType;
use AppBundle\Model\PropertyTypeFactory;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PropertyTypeTest extends WebTestCase
{

    /** @var  Property */
    protected $property;

    /** @var  PropertyType */
    protected $propertyType;

    /** @var  EntityManagerInterface */
    protected $em;

    /** @var Connection */
    protected $dbalConnection;

    public function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine.orm.default_entity_manager');

        $this->dbalConnection = static::$kernel->getContainer()
            ->get('doctrine.dbal.default_connection');

        $this->propertyType = PropertyTypeFactory::create(PropertyType::KX);

        $this->property = PropertyFactory::create()
            ->setName('Property')
            ->setPropertyType($this->propertyType)
        ;
    }

    public function testConvertNullToDataBaseValueReturnsNull() {
        $this->assertEquals(null, $this->dbalConnection->convertToDatabaseValue(null, 'property_type'));
    }

    public function testConvertToDatabase() {
        $this->assertEquals('kx', $this->dbalConnection->convertToDatabaseValue($this->propertyType, 'property_type'));
    }

    public function testConvertNullToPhpValueReturnsNull() {
        $this->assertEquals(null, $this->dbalConnection->convertToPHPValue(null, 'property_type'));
    }

    public function testConvertToPhpValue() {
        $this->assertEquals($this->propertyType, $this->dbalConnection->convertToPHPValue('kx', 'property_type'));
    }

    public function testSavePropertyTypeWithProperty()
    {
        $this->em->persist($this->property);
        $this->em->flush();
        $this->em->clear();
        
        $property = $this->em->getRepository('AppBundle:Property')->findOneBy(array('id' => $this->property->getId()->toString()));
        $this->assertEquals($this->propertyType, $property->getPropertyType());
        $this->em->remove($property);
        $this->em->flush();
    }
}