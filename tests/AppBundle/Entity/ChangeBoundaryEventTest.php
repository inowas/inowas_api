<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\BoundaryModelObject;
use AppBundle\Entity\ChangeBoundaryEvent;
use AppBundle\Entity\ModFlowModel;
use AppBundle\Model\ModFlowModelFactory;
use AppBundle\Model\WellBoundaryFactory;

class ChangeBoundaryEventTest extends \PHPUnit_Framework_TestCase
{
    /** @var ChangeBoundaryEvent */
    protected $event;

    /** @var  BoundaryModelObject */
    protected $boundary;

    /** @var  BoundaryModelObject */
    protected $changedBoundary;

    /** @var  ModFlowModel */
    protected $model;

    public function setUp(){
        $this->boundary = WellBoundaryFactory::createPublicWell();
        $this->boundary->setName('oldBoundary');

        $this->changedBoundary = WellBoundaryFactory::createPublicWell();
        $this->changedBoundary->setName('changedBoundary');

        $this->event = new ChangeBoundaryEvent($this->boundary, $this->changedBoundary);
    }


    public function testInstantiateChangeBoundaryEvent(){
        $this->assertInstanceOf(ChangeBoundaryEvent::class, $this->event);
    }

    public function testGetOrigin(){
        $this->assertEquals($this->boundary, $this->event->getOrigin());
    }

    public function testGetChangedBoundary(){
        $this->assertEquals($this->changedBoundary, $this->event->getNewBoundary());
    }

    public function testApplyEvent(){
        $model = ModFlowModelFactory::create();
        $model->addBoundary($this->boundary);
        $this->assertCount(1, $model->getBoundaries());
        $this->assertEquals($this->boundary, $model->getBoundaries()->first());
        $this->event->applyTo($model);
        $this->assertCount(1, $model->getBoundaries());
        $this->assertEquals($this->changedBoundary, $model->getBoundaries()->first());
    }
}
