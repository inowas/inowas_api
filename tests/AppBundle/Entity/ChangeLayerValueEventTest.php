<?php

namespace AppBundle\Tests\Entity;


use AppBundle\Entity\ChangeLayerValueEvent;
use AppBundle\Entity\GeologicalLayer;
use AppBundle\Entity\PropertyType;
use AppBundle\Entity\PropertyValue;
use AppBundle\Model\GeologicalUnitFactory;
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
        $this->layer = GeologicalUnitFactory::create();
        $this->propertyType = PropertyTypeFactory::create();
        $this->value = PropertyValueFactory::create();
    }

    public function testInstantiateChangeLayerValueEvent()
    {
        $changeLayerValueEvent = new ChangeLayerValueEvent($this->layer, $this->propertyType, $this->value);
        $this->assertInstanceOf('AppBundle\Entity\ChangeLayerValueEvent', $changeLayerValueEvent);
        $this->assertInstanceOf('Ramsey\Uuid\Uuid', $changeLayerValueEvent->getId());
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {}
}
