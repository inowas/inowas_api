<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\AddCalculationPropertiesEvent;
use AppBundle\Entity\ModFlowModel;
use AppBundle\Model\ModFlowModelFactory;
use Inowas\PyprocessingBundle\Model\Modflow\Package\FlopyCalculationProperties;
use Inowas\PyprocessingBundle\Model\Modflow\Package\FlopyCalculationPropertiesFactory;

class AddCalculationPropertiesEventTest extends \PHPUnit_Framework_TestCase
{

    /** @var  ModFlowModel */
    protected $model;

    /** @var  FlopyCalculationProperties */
    protected $calculationProperties;

    /**  */
    public function setUp(){
        $this->model = ModFlowModelFactory::create();
        $this->calculationProperties = FlopyCalculationPropertiesFactory::loadFromApiAndRun($this->model);
    }

    public function testInstantiationWithModel(){
        $this->assertInstanceOf(AddCalculationPropertiesEvent::class, new AddCalculationPropertiesEvent($this->calculationProperties));
    }

    public function testApplyToMethodToModelOverwritesCalculationProperties(){
        $event = new AddCalculationPropertiesEvent($this->calculationProperties);
        $this->assertNull($this->model->getCalculationProperties());
        $event->applyTo($this->model);
        $this->assertEquals($this->calculationProperties, $this->model->getCalculationProperties());
    }
}
