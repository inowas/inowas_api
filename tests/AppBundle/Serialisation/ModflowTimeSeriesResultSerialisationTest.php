<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\ModFlowModel;
use AppBundle\Model\ModFlowModelFactory;
use AppBundle\Model\ModflowProperties\ModflowTimeSeriesResultProperties;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class ModflowTimeSeriesResultSerialisationTest extends \PHPUnit_Framework_TestCase
{
    /** @var  Serializer $serializer */
    protected $serializer;

    /** @var  ModFlowModel */
    protected $model;

    /** @var ModflowTimeSeriesResultProperties */
    protected $modflowTimeSeriesResult;

    public function setUp()
    {
        /** @var ModFlowModel $model */
        $this->model = ModFlowModelFactory::create();
        $this->serializer = SerializerBuilder::create()->build();
        $this->modflowTimeSeriesResult = new ModflowTimeSeriesResultProperties(
            $this->model->getId()->toString(),
            2,
            3,
            4
        );
    }

    public function testModflowRasterResultProcess()
    {
        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modflowProcess');
        $modflowTimeSeriesResultJSON = $this->serializer->serialize($this->modflowTimeSeriesResult, 'json', $serializationContext);
        $modflowTimeSeries = json_decode($modflowTimeSeriesResultJSON);

        $this->assertObjectHasAttribute('model_id', $modflowTimeSeries);
        $this->assertEquals($this->model->getId()->toString(), $modflowTimeSeries->model_id);
        $this->assertObjectHasAttribute('calculation', $modflowTimeSeries);
        $this->assertEquals(false, $modflowTimeSeries->calculation);
        $this->assertObjectHasAttribute('result', $modflowTimeSeries);
        $this->assertEquals(true, $modflowTimeSeries->result);
        $this->assertObjectHasAttribute('output_type', $modflowTimeSeries);
        $this->assertEquals('time_series', $modflowTimeSeries->output_type);
        $this->assertObjectHasAttribute('layer', $modflowTimeSeries);
        $this->assertEquals($this->modflowTimeSeriesResult->getLayer(), $modflowTimeSeries->layer);
        $this->assertObjectHasAttribute('cell_x', $modflowTimeSeries);
        $this->assertEquals($this->modflowTimeSeriesResult->getCellX(), $modflowTimeSeries->cell_x);
        $this->assertObjectHasAttribute('cell_y', $modflowTimeSeries);
        $this->assertEquals($this->modflowTimeSeriesResult->getCellY(), $modflowTimeSeries->cell_y);
        $this->assertObjectHasAttribute('timesteps', $modflowTimeSeries);
        $this->assertEquals($this->modflowTimeSeriesResult->getTimesteps(), $modflowTimeSeries->timesteps);
    }
}
