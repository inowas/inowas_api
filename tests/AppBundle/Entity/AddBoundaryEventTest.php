<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\AddBoundaryEvent;
use AppBundle\Entity\Well;
use AppBundle\Model\WellFactory;

class AddBoundaryEventTest extends \PHPUnit_Framework_TestCase
{
    /** @var  Well */
    protected $well;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->well = WellFactory::create();
    }

    public function testInstantiateChangeLayerValueEvent()
    {
        $event = new AddBoundaryEvent($this->well);
        $this->assertInstanceOf('AppBundle\Entity\AddBoundaryEvent', $event);
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
