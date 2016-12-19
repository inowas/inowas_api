<?php

namespace Inowas\ScenarioAnalysisBundle\Tests\Model;


use Inowas\AppBundle\Model\User;
use Inowas\ModflowBundle\Model\ModflowModelFactory;
use Inowas\ScenarioAnalysisBundle\Factory\ScenarioAnalysisFactory;
use Inowas\ScenarioAnalysisBundle\Factory\ScenarioFactory;

class ScenarioAnalysisTest extends \PHPUnit_Framework_TestCase
{

    public function setUp(){}

    public function testRemoveScenario(){
        $user = new User();
        $model = ModflowModelFactory::create();
        $scenarioAnalysis = ScenarioAnalysisFactory::create($user, $model);

        $scenario1 = ScenarioFactory::create($model);
        $scenario2 = ScenarioFactory::create($model);
        $scenario3 = ScenarioFactory::create($model);
        $scenario4 = clone $scenario3;
        $scenario5 = clone $scenario4;


        $scenarioAnalysis->addScenario($scenario1);
        $scenarioAnalysis->addScenario($scenario2);
        $scenarioAnalysis->addScenario($scenario3);
        $scenarioAnalysis->addScenario($scenario4);
        $scenarioAnalysis->addScenario($scenario5);

        $json = json_encode($scenarioAnalysis->getScenarios()->toArray());
        $this->assertJson($json);
        $this->assertTrue(is_array(json_decode($json)));

        $scenarioAnalysis->removeScenario($scenario2);
        $json = json_encode($scenarioAnalysis->getScenarios()->toArray());
        $this->assertJson($json);
        $this->assertTrue(is_array(json_decode($json)));
        $this->assertTrue(true);
    }
}
