<?php

namespace Inowas\ScenarioAnalysisBundle\Tests\Model\Events;

use Inowas\ModflowBundle\Model\Boundary\WellBoundary;
use Inowas\ModflowBundle\Model\BoundaryFactory;

use Inowas\ModflowBundle\Model\StressPeriodFactory;
use Inowas\ScenarioAnalysisBundle\Model\Events\ChangeWellStressperiodsEvent;
use Inowas\ScenarioAnalysisBundle\Model\Events\ChangeWellTypeEvent;

class ChangeWellStressperiodsEventTest extends EventsBaseTest
{
    /** @var  WellBoundary */
    protected $well;

    public function setUp(){
        parent::setUp();

        $this->well = BoundaryFactory::createWel();
        $this->well->addStressPeriod(
            StressPeriodFactory::createWel()
        );

        $this->model->addBoundary($this->well);
    }

    public function testCanInstantiateEvent(){
        $event = new ChangeWellStressperiodsEvent($this->well->getId(), array());
        $this->assertInstanceOf(ChangeWellStressperiodsEvent::class, $event);
    }

    public function testApplyEventWithNoStressPeriod(){
        $boundary = $this->model->getBoundaries()->first();
        $this->assertCount(1, $boundary->getStressPeriods());

        $event = new ChangeWellStressperiodsEvent($this->well->getId(), array());
        $event->applyTo($this->model);

        /** @var WellBoundary $boundary */
        $boundary = $this->model->getBoundaries()->first();
        $this->assertCount(0, $boundary->getStressPeriods());
    }

    public function testApplyEventWithStressPeriods(){

        $stressPeriods = [];
        $stressPeriods[] = StressPeriodFactory::createWel()->setDateTimeBegin(new \DateTime('2015-01-01'))->setFlux(-1000);
        $stressPeriods[] = StressPeriodFactory::createWel()->setDateTimeBegin(new \DateTime('2015-02-01'))->setFlux(-2000);
        $stressPeriods[] = StressPeriodFactory::createWel()->setDateTimeBegin(new \DateTime('2015-03-01'))->setFlux(-3000);
        $stressPeriods[] = StressPeriodFactory::createWel()->setDateTimeBegin(new \DateTime('2015-04-01'))->setFlux(-4000);
        $stressPeriods[] = StressPeriodFactory::createWel()->setDateTimeBegin(new \DateTime('2015-05-01'))->setFlux(-5000);

        $event = new ChangeWellStressperiodsEvent($this->well->getId(), $stressPeriods);
        $event->applyTo($this->model);

        /** @var WellBoundary $boundary */
        $boundary = $this->model->getBoundaries()->first();
        $this->assertCount(5, $boundary->getStressPeriods());
        $this->assertEquals($stressPeriods, $boundary->getStressPeriods()->toArray());
    }


    public function tearDown()
    {
        parent::tearDown();
        unset($this->well);
    }
}
