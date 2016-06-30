<?php

namespace Tests\AppBundle\Model;

use AppBundle\Model\EventFactory;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\PropertyTypeFactory;
use AppBundle\Model\PropertyValueFactory;
use AppBundle\Model\WellFactory;

class EventFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testCreateAddBoundaryEvent()
    {
        $this->assertInstanceOf('AppBundle\Entity\AddBoundaryEvent', EventFactory::createAddBoundaryEvent(
            WellFactory::create()
        ));
    }

    public function testCreateChangeLayerValueEvent()
    {
        $this->assertInstanceOf('AppBundle\Entity\ChangeLayerValueEvent', EventFactory::createChangeLayerValueEvent(
            GeologicalLayerFactory::create(),
            PropertyTypeFactory::create(),
            PropertyValueFactory::create()
        ));
    }
}
