<?php

namespace Inowas\ScenarioAnalysisBundle\Tests\Model\Events;

use Inowas\ModflowBundle\Model\Boundary\WellBoundary;
use Inowas\ModflowBundle\Model\BoundaryFactory;

use Inowas\ScenarioAnalysisBundle\Model\Events\ChangeWellTypeEvent;
use Inowas\ScenarioAnalysisBundle\Model\Events\RemoveWellEvent;

class RemoveWellEventTest extends EventsBaseTest
{
    /** @var  WellBoundary */
    protected $well;

    public function setUp(){
        parent::setUp();

        $this->well = BoundaryFactory::createWel();
        $this->model->addBoundary($this->well);
    }

    public function testCanInstantiateEvent(){
        $event = new RemoveWellEvent($this->well->getId());
        $this->assertInstanceOf(RemoveWellEvent::class, $event);
    }

    public function testApplyEvent(){
        $event = new RemoveWellEvent($this->well->getId());
        $event->applyTo($this->model);

        /** @var WellBoundary $boundary */
        $this->assertCount(0, $this->model->getBoundaries());
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->well);
    }
}
