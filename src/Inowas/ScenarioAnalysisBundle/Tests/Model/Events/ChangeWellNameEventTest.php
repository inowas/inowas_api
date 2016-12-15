<?php

namespace Inowas\ScenarioAnalysisBundle\Tests\Model\Events;

use Inowas\ModflowBundle\Model\Boundary\WellBoundary;
use Inowas\ModflowBundle\Model\BoundaryFactory;

use Inowas\ScenarioAnalysisBundle\Model\Events\ChangeWellNameEvent;

class ChangeWellNameEventTest extends EventsBaseTest
{
    /** @var  WellBoundary */
    protected $well;


    public function setUp(){
        parent::setUp();

        $this->well = BoundaryFactory::createWel()->setName('Test123');
        $this->model->addBoundary($this->well);
    }

    public function testCanInstantiateEvent(){
        $event = new ChangeWellNameEvent($this->well->getId(), 'Test345');
        $this->assertInstanceOf(ChangeWellNameEvent::class, $event);
    }

    public function testApplyEvent(){
        $event = new ChangeWellNameEvent($this->well->getId(), 'Test345');
        $event->applyTo($this->model);

        /** @var WellBoundary $boundary */
        $boundary = $this->model->getBoundaries()->first();
        $this->assertEquals('Test345', $boundary->getName());
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->well);
    }
}
