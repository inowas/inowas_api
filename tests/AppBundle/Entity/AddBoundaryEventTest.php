<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\AddBoundaryEvent;
use AppBundle\Entity\PropertyType;
use AppBundle\Entity\PropertyValue;
use AppBundle\Entity\Well;
use AppBundle\Model\PropertyTypeFactory;
use AppBundle\Model\PropertyValueFactory;
use AppBundle\Model\WellFactory;

class AddBoundaryEventTest extends \PHPUnit_Framework_TestCase
{
    /** @var  Well */
    protected $well;

    /** @var  PropertyType */
    protected $propertyType;

    /** @var  PropertyValue */
    protected $value;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->well = WellFactory::create();
        $this->propertyType = PropertyTypeFactory::create();
        $this->value = PropertyValueFactory::create();
    }

    public function testInstantiateChangeLayerValueEvent()
    {
        $event = new AddBoundaryEvent($this->well);
        $this->assertInstanceOf('AppBundle\Entity\AddBoundaryEvent', $event);
        $this->assertInstanceOf('Ramsey\Uuid\Uuid', $event->getId());
    }

    public function testGetLayer()
    {
        $event = new AddBoundaryEvent($this->well);
        $this->assertEquals($this->well, $event->getBoundary());
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {}
}
