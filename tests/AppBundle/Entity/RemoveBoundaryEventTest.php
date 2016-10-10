<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\RemoveBoundaryEvent;
use AppBundle\Entity\WellBoundary;
use AppBundle\Model\ModFlowModelFactory;
use AppBundle\Model\WellBoundaryFactory;

class RemoveBoundaryEventTest extends \PHPUnit_Framework_TestCase
{

    /** @var  RemoveBoundaryEvent */
    protected $event;

    /** @var  WellBoundary */
    protected $well;

    /**
     * {@inheritDoc}
     */
    public function setUp(){
        $this->well = WellBoundaryFactory::create();
        $this->event = new RemoveBoundaryEvent($this->well);
    }

    public function testInstantiateRemoveEvent(){
        $this->assertInstanceOf(RemoveBoundaryEvent::class, $this->event);
    }

    public function testApplyToModelWithBoundary(){
        $model = ModFlowModelFactory::create();
        $model->addBoundary($this->well);
        $this->assertCount(1, $model->getBoundaries());
        $this->assertEquals($this->well, $model->getBoundaries()->first());

        $this->event->applyTo($model);
        $this->assertCount(0, $model->getBoundaries());
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {}
}
