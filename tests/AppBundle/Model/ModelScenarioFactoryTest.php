<?php

namespace Tests\AppBundle\Model;

use AppBundle\Entity\ModflowModelScenario;
use AppBundle\Model\ModelScenarioFactory;
use AppBundle\Model\ModFlowModelFactory;

class ModelScenarioFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiate()
    {
        $this->assertInstanceOf(ModflowModelScenario::class, ModelScenarioFactory::create(ModFlowModelFactory::create()));
    }
}
