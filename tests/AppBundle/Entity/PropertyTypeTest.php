<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\PropertyType;
use AppBundle\Model\PropertyTypeFactory;

class PropertyTypeTest extends \PHPUnit_Framework_TestCase
{

    /** @var PropertyType */
    protected $propertyType;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->propertyType = PropertyTypeFactory::create();
    }

    public function testInstantiation()
    {
        $this->assertInstanceOf('Ramsey\Uuid\Uuid', $this->propertyType->getId());
        $this->assertEquals(PropertyType::STATIC_AND_TIME_DEPENDENT_VALUES, $this->propertyType->getValueType());
    }

    public function testSetGetAbbreviation()
    {
        $abbreviation = "abb";
        $this->propertyType->setAbbreviation($abbreviation);
        $this->assertEquals($abbreviation, $this->propertyType->getAbbreviation());
    }

    public function testSetGetName()
    {
        $name = "name";
        $this->propertyType->setName($name);
        $this->assertEquals($name, $this->propertyType->getName());
    }

    public function testSetGetValueType()
    {
        $valueType = "vt";
        $this->propertyType->setValueType($valueType);
        $this->assertEquals($valueType, $this->propertyType->getValueType());
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        unset($this->areaType);
    }
}
