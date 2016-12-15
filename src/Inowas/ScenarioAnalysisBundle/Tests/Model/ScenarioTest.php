<?php

namespace Inowas\ScenarioAnalysisBundle\Tests\Model;


use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ModflowBundle\Model\ModflowModelFactory;
use Inowas\ScenarioAnalysisBundle\Factory\ScenarioFactory;
use Inowas\ScenarioAnalysisBundle\Model\Scenario;

class ScenarioTest extends \PHPUnit_Framework_TestCase
{
    /** @var  ModflowModel */
    protected $model;

    /** @var  Scenario */
    protected $scenario;

    public function setUp()
    {
        $this->model = ModflowModelFactory::create();
        $this->scenario =  ScenarioFactory::create($this->model);
    }

    public function testCanInstantiateScenario()
    {
        $this->assertInstanceOf(ModflowModel::class, $this->model);
        $this->assertInstanceOf(Scenario::class, $this->scenario);
    }

    public function tearDown()
    {
        unset($this->model);
        unset($this->scenario);
    }
}
