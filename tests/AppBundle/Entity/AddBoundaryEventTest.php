<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\AddBoundaryEvent;
use AppBundle\Entity\WellBoundary;
use AppBundle\Model\WellBoundaryFactory;

class AddBoundaryEventTest extends \PHPUnit_Framework_TestCase
{
    /** @var  WellBoundary */
    protected $well;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->well = WellBoundaryFactory::create();
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
