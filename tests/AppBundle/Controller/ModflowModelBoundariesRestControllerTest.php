<?php

namespace Metadata\Tests\Driver\Fixture\Tests\AppBundle\Controller;

use AppBundle\Entity\ModFlowModel;
use AppBundle\Entity\ModflowModelScenario;
use AppBundle\Model\ModFlowModelFactory;
use AppBundle\Model\ModFlowModelScenarioFactory;
use AppBundle\Model\WellBoundaryFactory;

class ModflowModelBoundariesRestControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ModFlowModel $model
     */
    protected $model;

    /**
     * @var ModflowModelScenario
     */
    protected $scenario;

    public function setUp(){
        $this->model = ModFlowModelFactory::create();
        $this->scenario = ModFlowModelScenarioFactory::create($this->model);
    }

    public function testModflowModelHasNoBoundaries(){
        $this->assertCount(0, $this->model->getBoundaries());
    }

    public function testScenarioHasModel(){
        $this->assertEquals($this->model, $this->scenario->getModel());
    }

    public function testApplyScenarioBoundaryToModel(){
        $well = WellBoundaryFactory::create();
        $this->scenario->addBoundary($well);
        $this->assertCount(0, $this->scenario->getBaseModel()->getBoundaries());
        $this->assertCount(1, $this->scenario->getModel()->getBoundaries());
    }
}
