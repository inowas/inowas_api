<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\AddBoundaryEvent;
use AppBundle\Entity\WellBoundary;
use AppBundle\Model\ModFlowModelFactory;
use AppBundle\Model\WellBoundaryFactory;

class AddBoundaryEventTest extends \PHPUnit_Framework_TestCase
{
    /** @var  WellBoundary */
    protected $well;

    /** @var  AddBoundaryEvent $event */
    protected $event;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->well = WellBoundaryFactory::create();
        $this->event = new AddBoundaryEvent($this->well);
    }

    public function testInstantiateChangeLayerValueEvent()
    {
        $this->assertInstanceOf(AddBoundaryEvent::class, $this->event);
    }

    public function testGetBoundary()
    {
        $this->assertEquals($this->well, $this->event->getBoundary());
    }

    public function testApplyToModel()
    {
        $model = ModFlowModelFactory::create();
        $this->assertCount(0, $model->getBoundaries());
        $this->event->applyTo($model);
        $this->assertCount(1, $model->getBoundaries());
        $this->assertEquals($this->well, $model->getBoundaries()->first());
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        unset($this->well);
        unset($this->event);
    }
}
