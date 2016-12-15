<?php

namespace Inowas\ScenarioAnalysisBundle\Tests\Model\Events;

use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ModflowBundle\Model\ModflowModelFactory;
use Inowas\ScenarioAnalysisBundle\Factory\ScenarioFactory;
use Inowas\ScenarioAnalysisBundle\Model\Scenario;

class AddWellEventTest extends \PHPUnit_Framework_TestCase
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

    public function testCanInstantiateAddWellEvent(){

    }

}
