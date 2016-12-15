<?php

namespace Inowas\ScenarioAnalysisBundle\Tests\Model\Events;

use Inowas\ModflowBundle\Model\Boundary\WellBoundary;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ModflowBundle\Model\ModflowModelFactory;
use Inowas\ScenarioAnalysisBundle\Factory\ScenarioFactory;
use Inowas\ScenarioAnalysisBundle\Model\Events\AddWellEvent;
use Inowas\ScenarioAnalysisBundle\Model\Scenario;

class AddWellEventTest extends \PHPUnit_Framework_TestCase
{
    /** @var  AddWellEvent */
    protected $event;

    /** @var  ModflowModel */
    protected $model;

    /** @var  Scenario */
    protected $scenario;

    public function setUp()
    {
        $this->model = ModflowModelFactory::create();
        $this->scenario =  ScenarioFactory::create($this->model);
    }

    public function testCanInstantiateAddWellEvent(){
        $this->event = AddWellEvent::fromName('TestWell');
        $this->assertInstanceOf(AddWellEvent::class, $this->event);
    }

    public function testApplyAddWellEvent(){
        $this->event = AddWellEvent::fromName('TestWell');
        $this->event->applyTo($this->model);
        $this->assertCount(1, $this->model->getBoundaries());

        /** @var WellBoundary $boundary */
        $boundary = $this->model->getBoundaries()->first();
        $this->assertInstanceOf(WellBoundary::class, $boundary);
        $this->assertEquals('TestWell', $boundary->getName());
    }

    public function tearDown(){
        unset($this->event);
        unset($this->model);
        unset($this->scenario);
    }

}
