<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\RemoveEvent;
use AppBundle\Entity\WellBoundary;
use AppBundle\Model\ModFlowModelFactory;
use AppBundle\Model\ObservationPointFactory;
use AppBundle\Model\WellBoundaryFactory;

class RemoveEventTest extends \PHPUnit_Framework_TestCase
{

    /** @var  RemoveEvent */
    protected $event;

    /** @var  WellBoundary */
    protected $well;

    /**
     * {@inheritDoc}
     */
    public function setUp(){
        $this->well = WellBoundaryFactory::create();
        $this->event = new RemoveEvent($this->well);
    }

    public function testInstantiateRemoveEvent(){
        $this->assertInstanceOf(RemoveEvent::class, $this->event);
    }

    public function testApplyToModelWithBoundary(){
        $model = ModFlowModelFactory::create();
        $model->addBoundary($this->well);
        $this->assertCount(1, $model->getBoundaries());
        $this->assertEquals($this->well, $model->getBoundaries()->first());

        $this->event->applyTo($model);
        $this->assertCount(0, $model->getBoundaries());
    }

    public function testApplyToModelWithModelObject(){
        $model = ModFlowModelFactory::create();
        $model->addModelObject($this->well);
        $this->assertCount(1, $model->getModelObjects());
        $this->assertEquals($this->well, $model->getModelObjects()->first());

        $this->event->applyTo($model);
        $this->assertCount(0, $model->getModelObjects());
    }

    public function testApplyToModelWithObservationPoint(){
        $observationPoint = ObservationPointFactory::create();
        $event = new RemoveEvent($observationPoint);

        $model = ModFlowModelFactory::create();
        $model->addObservationPoint($observationPoint);
        $this->assertCount(1, $model->getObservationPoints());
        $this->assertEquals($observationPoint, $model->getObservationPoints()->first());

        $event->applyTo($model);
        $this->assertCount(0, $model->getObservationPoints());
    }



    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {}
}
