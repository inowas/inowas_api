<?php

namespace Inowas\ScenarioAnalysisBundle\Tests\Model\Events;

use Inowas\ModflowBundle\Model\Boundary\WellBoundary;
use Inowas\ScenarioAnalysisBundle\Model\Events\AddWellEvent;

class AddWellEventTest extends EventsBaseTest
{
    /** @var  AddWellEvent */
    protected $event;

    public function testCanInstantiateAddWellEvent(){
        $this->event = new AddWellEvent('TestWell');
        $this->assertInstanceOf(AddWellEvent::class, $this->event);
    }

    public function testApplyEvent(){
        $this->event = new AddWellEvent('TestWell');
        $this->event->applyTo($this->model);
        $this->assertCount(1, $this->model->getBoundaries());

        /** @var WellBoundary $boundary */
        $boundary = $this->model->getBoundaries()->first();
        $this->assertInstanceOf(WellBoundary::class, $boundary);
        $this->assertEquals('TestWell', $boundary->getName());
    }

    public function tearDown(){
        parent::tearDown();
        unset($this->event);
    }
}
