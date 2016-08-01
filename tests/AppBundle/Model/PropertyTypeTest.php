<?php

namespace Tests\AppBundle\Model;

use AppBundle\Model\PropertyType;
use AppBundle\Model\PropertyTypeFactory;

class PropertyTypeTest extends \PHPUnit_Framework_TestCase
{

    /** @var PropertyType */
    protected $propertyType;

    /**
     * {@inheritDoc}
     */
    public function setUp(){
        $this->propertyType = PropertyTypeFactory::create(PropertyType::KX);
    }

    public function testInstantiation()
    {
        $this->assertInstanceOf(PropertyType::class, $this->propertyType);
        $this->assertEquals(PropertyType::STATIC_VALUE_ONLY, $this->propertyType->getValueType());
    }

    public function testSetGetAbbreviation()
    {
        $abbreviation = "kx";
        $this->assertEquals($abbreviation, $this->propertyType->getAbbreviation());
    }
}
