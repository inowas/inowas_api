<?php

namespace Inowas\ScenarioAnalysisBundle\Tests\Model\Events;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Inowas\ModflowBundle\Model\Boundary\WellBoundary;
use Inowas\ModflowBundle\Model\BoundaryFactory;
use Inowas\ScenarioAnalysisBundle\Model\Events\MoveWellEvent;

class MoveWellEventTest extends EventsBaseTest
{
    /** @var  WellBoundary */
    protected $well;

    public function setUp(){
        parent::setUp();

        $this->well = BoundaryFactory::createWel()
            ->setGeometry(new Point(1,2,4326));

        $this->model->addBoundary($this->well);
    }

    public function testCanInstantiateEvent(){
        $event = new MoveWellEvent($this->well->getId(), new Point(2,3,4326));
        $this->assertInstanceOf(MoveWellEvent::class, $event);
    }

    public function testApplyEvent(){
        $event = new MoveWellEvent($this->well->getId(), new Point(2,3,4326));
        $event->applyTo($this->model);

        /** @var WellBoundary $boundary */
        $boundary = $this->model->getBoundaries()->first();
        $this->assertEquals(new Point(2,3,4326), $boundary->getGeometry());
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->well);
    }
}
