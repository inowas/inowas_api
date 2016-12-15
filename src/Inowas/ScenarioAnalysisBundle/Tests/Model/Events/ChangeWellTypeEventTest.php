<?php

namespace Inowas\ScenarioAnalysisBundle\Tests\Model\Events;

use Inowas\ModflowBundle\Model\Boundary\WellBoundary;
use Inowas\ModflowBundle\Model\BoundaryFactory;

use Inowas\ScenarioAnalysisBundle\Model\Events\ChangeWellTypeEvent;

class ChangeWellTypeEventTest extends EventsBaseTest
{
    /** @var  WellBoundary */
    protected $well;

    public function setUp(){
        parent::setUp();

        $this->well = BoundaryFactory::createWel()->setWellType('Test123');
        $this->model->addBoundary($this->well);
    }

    public function testCanInstantiateEvent(){
        $event = new ChangeWellTypeEvent($this->well->getId(), 'Test345');
        $this->assertInstanceOf(ChangeWellTypeEvent::class, $event);
    }

    public function testApplyEvent(){
        $event = new ChangeWellTypeEvent($this->well->getId(), 'Test345');
        $event->applyTo($this->model);

        /** @var WellBoundary $boundary */
        $boundary = $this->model->getBoundaries()->first();
        $this->assertEquals('Test345', $boundary->getWellType());
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->well);
    }
}
