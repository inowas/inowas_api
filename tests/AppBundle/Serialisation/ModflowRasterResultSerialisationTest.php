<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\ModFlowModel;
use AppBundle\Model\ModFlowModelFactory;
use AppBundle\Model\ModflowProperties\ModflowRasterResultProperties;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class ModflowRasterResultSerialisationTest extends \PHPUnit_Framework_TestCase
{
    /** @var  Serializer $serializer */
    protected $serializer;

    /** @var  ModFlowModel */
    protected $model;

    /** @var ModflowRasterResultProperties */
    protected $modflowRasterResult;

    public function setUp()
    {
        /** @var ModFlowModel $model */
        $this->model = ModFlowModelFactory::create();
        $this->serializer = SerializerBuilder::create()->build();
        $this->modflowRasterResult = new ModflowRasterResultProperties(
            $this->model->getId()->toString(),
            2,
            ModflowRasterResultProperties::OP_DELTA
        );
        $this->modflowRasterResult->addTimestep(3);
        $this->modflowRasterResult->addTimestep(4);
    }

    public function testModflowRasterResultProcess()
    {
        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modflowProcess');
        $modflowRasterJSON = $this->serializer->serialize($this->modflowRasterResult, 'json', $serializationContext);
        $modflowRaster = json_decode($modflowRasterJSON);

        $this->assertObjectHasAttribute('model_id', $modflowRaster);
        $this->assertEquals($this->model->getId()->toString(), $modflowRaster->model_id);
        $this->assertObjectHasAttribute('calculation', $modflowRaster);
        $this->assertEquals(false, $modflowRaster->calculation);
        $this->assertObjectHasAttribute('result', $modflowRaster);
        $this->assertEquals(true, $modflowRaster->result);
        $this->assertObjectHasAttribute('output_type', $modflowRaster);
        $this->assertEquals('raster', $modflowRaster->output_type);
        $this->assertObjectHasAttribute('layer', $modflowRaster);
        $this->assertEquals($this->modflowRasterResult->getLayer(), $modflowRaster->layer);
        $this->assertObjectHasAttribute('timesteps', $modflowRaster);
        $this->assertEquals($this->modflowRasterResult->getTimesteps(), $modflowRaster->timesteps);
        $this->assertObjectHasAttribute('operation', $modflowRaster);
        $this->assertEquals($this->modflowRasterResult->getOperation(), $modflowRaster->operation);
    }
}
