<?php

namespace Tests\Inowas\ModflowBundle\Model;

use Inowas\ModflowBundle\Model\ModflowResultTimeSeriesParameter;

class ModflowResultTimeSeriesParameterTest extends \PHPUnit_Framework_TestCase
{
    protected $modflowResultsTimeseriesParameter;

    public function testInstantiateAndSerialize()
    {
        $modelId = '123';
        $layer = 2;
        $row = 3;
        $column = 4;
        $operation = ModflowResultTimeSeriesParameter::OP_RAW;


        $this->modflowResultsTimeseriesParameter = new ModflowResultTimeSeriesParameter(
            $modelId, $layer, $row, $column, $operation
        );

        $serializedObject = json_encode($this->modflowResultsTimeseriesParameter);
        $this->assertObjectHasAttribute('model_id', json_decode($serializedObject));
        $this->assertEquals('123', json_decode($serializedObject)->model_id);
        $this->assertObjectHasAttribute('layer', json_decode($serializedObject));
        $this->assertEquals(2, json_decode($serializedObject)->layer);
        $this->assertObjectHasAttribute('cell_y', json_decode($serializedObject));
        $this->assertEquals(3, json_decode($serializedObject)->cell_y);
        $this->assertObjectHasAttribute('cell_x', json_decode($serializedObject));
        $this->assertEquals(4, json_decode($serializedObject)->cell_x);
        $this->assertObjectHasAttribute('operation', json_decode($serializedObject));
        $this->assertEquals(ModflowResultTimeSeriesParameter::OP_RAW, json_decode($serializedObject)->operation);
    }

    public function testThrowsExceptionIfOperationNotKnown(){
        $modelId = '123';
        $layer = 2;
        $row = 3;
        $column = 4;
        $operation = 'foo';

        $this->setExpectedException('AppBundle\Exception\InvalidArgumentException');
        $this->modflowResultsTimeseriesParameter = new ModflowResultTimeSeriesParameter(
            $modelId, $layer, $row, $column, $operation
        );
    }
}
