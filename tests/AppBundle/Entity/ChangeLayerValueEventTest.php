<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\ChangeLayerValueEvent;
use AppBundle\Entity\GeologicalLayer;
use AppBundle\Entity\PropertyValue;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\PropertyType;
use AppBundle\Model\PropertyTypeFactory;
use AppBundle\Model\PropertyValueFactory;

class ChangeLayerValueEventTest extends \PHPUnit_Framework_TestCase
{

    /** @var  GeologicalLayer */
    protected $layer;

    /** @var  PropertyType */
    protected $propertyType;

    /** @var  PropertyValue */
    protected $value;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->layer = GeologicalLayerFactory::create();
        $this->propertyType = PropertyTypeFactory::create(PropertyType::KX);
        $this->value = PropertyValueFactory::create();
    }

    public function testInstantiateChangeLayerValueEvent()
    {
        $changeLayerValueEvent = new ChangeLayerValueEvent($this->layer, $this->propertyType, $this->value);
        $this->assertInstanceOf('AppBundle\Entity\ChangeLayerValueEvent', $changeLayerValueEvent);
    }

    public function testGetLayer()
    {
        $changeLayerValueEvent = new ChangeLayerValueEvent($this->layer, $this->propertyType, $this->value);
        $this->assertEquals($this->layer, $changeLayerValueEvent->getLayer());
    }

    public function testGetPropertyType()
    {
        $changeLayerValueEvent = new ChangeLayerValueEvent($this->layer, $this->propertyType, $this->value);
        $this->assertEquals($this->propertyType, $changeLayerValueEvent->getPropertyType());
    }

    public function testGetValue()
    {
        $changeLayerValueEvent = new ChangeLayerValueEvent($this->layer, $this->propertyType, $this->value);
        $this->assertEquals($this->value, $changeLayerValueEvent->getValue());
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {}
}
