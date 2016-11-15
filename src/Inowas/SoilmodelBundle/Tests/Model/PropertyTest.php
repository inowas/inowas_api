<?php

namespace Inowas\Soilmodel\Tests\Model;

use Inowas\Soilmodel\Model\BoreHoleLayerPropertyValue;
use Inowas\Soilmodel\Model\Property;
use Inowas\Soilmodel\Model\PropertyType;
use Inowas\Soilmodel\Model\PropertyValueInterface;

class PropertyTest extends \PHPUnit_Framework_TestCase
{

    /** @var  Property */
    private $property;

    public function setUp()
    {
        $this->property = new Property(
            PropertyType::fromString('kx'),
            BoreHoleLayerPropertyValue::fromValue(1.0)
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
        $this->assertInstanceOf(PropertyValueInterface::class, $this->property->getValue());
        $this->assertInstanceOf(BoreHoleLayerPropertyValue::class, $this->property->getValue());
    }

    public function tearDown()
    {
        unset($this->property);
    }
}
