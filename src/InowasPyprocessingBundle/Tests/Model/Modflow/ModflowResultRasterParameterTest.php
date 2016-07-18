<?php

namespace InowasPyprocessingBundle\Tests\Model\Modflow;

use InowasPyprocessingBundle\Exception\InvalidArgumentException;
use InowasPyprocessingBundle\Model\Modflow\ModflowResultRasterParameter;

class ModflowResultRasterParameterTest extends \PHPUnit_Framework_TestCase
{

    protected $modflowResultsRasterParameter;

    public function testInstantiateAndSerialize()
    {
        $modelId = '123';
        $layer = 2;
        $timesteps = array(1,2,3);
        $stressPeriods = array(4,5,6);
        $operation = ModflowResultRasterParameter::OP_RAW;


        $this->modflowResultsRasterParameter = new ModflowResultRasterParameter(
            $modelId, $layer, $timesteps, $stressPeriods, $operation
        );

        $serializedObject = json_encode($this->modflowResultsRasterParameter);
        $this->assertObjectHasAttribute('model_id', json_decode($serializedObject));
        $this->assertEquals('123', json_decode($serializedObject)->model_id);
        $this->assertObjectHasAttribute('layer', json_decode($serializedObject));
        $this->assertEquals(2, json_decode($serializedObject)->layer);
        $this->assertObjectHasAttribute('time_steps', json_decode($serializedObject));
        $this->assertEquals(array(1,2,3), json_decode($serializedObject)->time_steps);
        $this->assertObjectHasAttribute('stress_periods', json_decode($serializedObject));
        $this->assertEquals(array(4,5,6), json_decode($serializedObject)->stress_periods);
        $this->assertObjectHasAttribute('operation', json_decode($serializedObject));
        $this->assertEquals(ModflowResultRasterParameter::OP_RAW, json_decode($serializedObject)->operation);
    }

    public function testThrowsExceptionIfOperationNotKnown(){
        $modelId = '123';
        $layer = 2;
        $timesteps = array(1,2,3);
        $stressPeriods = array(4,5,6);
        $operation = 'foo';

        $this->setExpectedException(InvalidArgumentException::class);
        $this->modflowResultsRasterParameter = new ModflowResultRasterParameter(
            $modelId, $layer, $timesteps, $stressPeriods, $operation
        );
    }

}
