<?php

namespace Tests\AppBundle\Model;

use AppBundle\Entity\AddBoundaryEvent;
use AppBundle\Entity\ChangeLayerValueEvent;
use AppBundle\Model\EventFactory;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\PropertyType;
use AppBundle\Model\PropertyTypeFactory;
use AppBundle\Model\PropertyValueFactory;
use AppBundle\Model\WellBoundaryFactory;

class EventFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testCreateAddBoundaryEvent()
    {
        $this->assertInstanceOf(AddBoundaryEvent::class, EventFactory::createAddBoundaryEvent(
            WellBoundaryFactory::create()
        ));
    }

    public function testCreateChangeLayerValueEvent()
    {
        $this->assertInstanceOf(ChangeLayerValueEvent::class, EventFactory::createChangeLayerValueEvent(
            GeologicalLayerFactory::create(),
            PropertyTypeFactory::create(PropertyType::KX),
            PropertyValueFactory::create()
        ));
    }
}
