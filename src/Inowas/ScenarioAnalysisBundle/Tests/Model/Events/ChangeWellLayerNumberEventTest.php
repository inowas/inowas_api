<?php

namespace Inowas\ScenarioAnalysisBundle\Tests\Model\Events;

use Inowas\ModflowBundle\Model\Boundary\WellBoundary;
use Inowas\ModflowBundle\Model\BoundaryFactory;
use Inowas\ScenarioAnalysisBundle\Model\Events\ChangeWellLayerNumberEvent;

class ChangeWellLayerNumberEventTest extends EventsBaseTest
{
    /** @var  WellBoundary */
    protected $well;


    public function setUp(){
        parent::setUp();

        $this->well = BoundaryFactory::createWel()->setLayerNumber(5);
        $this->model->addBoundary($this->well);
    }

    public function testCanInstantiateEvent(){
        $event = new ChangeWellLayerNumberEvent($this->well->getId(), 2);
        $this->assertInstanceOf(ChangeWellLayerNumberEvent::class, $event);
    }

    public function testApplyEvent(){
        $event = new ChangeWellLayerNumberEvent($this->well->getId(), 2);
        $event->applyTo($this->model);

        /** @var WellBoundary $boundary */
        $boundary = $this->model->getBoundaries()->first();
        $this->assertEquals(2, $boundary->getLayerNumber());
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->well);
    }
}
