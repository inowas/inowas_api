<?php

namespace Tests\AppBundle\Model;

use AppBundle\Model\ModelScenarioFactory;
use AppBundle\Model\ModFlowModelFactory;

class ModelScenarioFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiate()
    {
        $this->assertInstanceOf('AppBundle\Entity\ModelScenario', ModelScenarioFactory::create(ModFlowModelFactory::create()));
    }
}
