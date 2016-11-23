<?php

namespace Inowas\Soilmodel\Tests\Model;

use Inowas\SoilmodelBundle\Model\Property;
use Inowas\SoilmodelBundle\Model\PropertyType;
use Inowas\SoilmodelBundle\Model\PropertyValue;

class PropertyTest extends \PHPUnit_Framework_TestCase
{

    /** @var  Property */
    private $property;

    public function setUp()
    {
        $this->property = new Property(
            PropertyType::fromString('kx'),
            PropertyValue::fromValue(1.0)
        );
    }

    public function testCanInstantiate()
    {
        $this->assertInstanceOf(Property::class, $this->property);
    }

    public function testGetPropertyType(){
        $this->assertInstanceOf(PropertyType::class, $this->property->getType());
    }

    public function testGetPropertyValue(){
        $this->assertInstanceOf(PropertyValue::class, $this->property->getValue());
    }

    public function tearDown()
    {
        unset($this->property);
    }
}