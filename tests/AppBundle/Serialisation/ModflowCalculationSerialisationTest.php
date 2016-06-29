<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\ModFlowModel;
use AppBundle\Model\ModFlowModelFactory;
use AppBundle\Model\ModflowProperties\ModflowCalculationProperties;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class ModflowCalculationSerialisationTest extends \PHPUnit_Framework_TestCase
{
    /** @var  Serializer $serializer */
    protected $serializer;

    /** @var  ModFlowModel */
    protected $model;

    /** @var ModflowCalculationProperties */
    protected $modflowCalculation;

    public function setUp()
    {
        /** @var ModFlowModel $model */
        $this->model = ModFlowModelFactory::create();
        $this->serializer = SerializerBuilder::create()->build();
        $this->modflowCalculation = new ModflowCalculationProperties($this->model->getId()->toString());
    }

    public function testModflowCalculationProcess()
    {
        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modflowProcess');
        $modflowCalculationJSON = $this->serializer->serialize($this->modflowCalculation, 'json', $serializationContext);
        $modflowCalculation = json_decode($modflowCalculationJSON);

        $this->assertObjectHasAttribute('model_id', $modflowCalculation);
        $this->assertEquals($this->model->getId()->toString(), $modflowCalculation->model_id);
        $this->assertObjectHasAttribute('calculation', $modflowCalculation);
        $this->assertEquals(true, $modflowCalculation->calculation);
        $this->assertObjectHasAttribute('result', $modflowCalculation);
        $this->assertEquals(false, $modflowCalculation->result);
    }
}
