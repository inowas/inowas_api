<?php

namespace Tests\Inowas\ModflowBundle\Model;

use Inowas\ModflowBundle\Model\ModflowCalculationParameter;

class ModflowCalculationParameterTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \Inowas\ModflowBundle\Model\ModflowCalculationParameter */
    protected $modflowCalculation;

    /** @var  string */
    protected $modelId;

    /** @var  string */
    protected $baseUrl;
    
    public function setUp()
    {
        $this->modelId = 'e33b7db0-43a0-43be-a502-fb4f24efd0cc';
        $this->baseUrl = 'http://app.dev.inowas.com';
        $this->modflowCalculation = new ModflowCalculationParameter($this->modelId, $this->baseUrl);
    }

    public function testInstantiate(){
        $this->assertInstanceOf('Inowas\ModflowBundle\Model\ModflowCalculationParameter', $this->modflowCalculation);
    }
    
    public function testSerialize(){
        $expectedResult = '{"model_id":"e33b7db0-43a0-43be-a502-fb4f24efd0cc","base_url":"http://app.dev.inowas.com"}';
        $this->assertEquals($expectedResult, json_encode($this->modflowCalculation, JSON_UNESCAPED_SLASHES));
    }

    public function testSerializeEncodeDecode(){
        $expectedResult = json_decode('{"model_id": "e33b7db0-43a0-43be-a502-fb4f24efd0cc","base_url":"http://app.dev.inowas.com"}');
        $this->assertEquals($expectedResult, json_decode(json_encode($this->modflowCalculation, JSON_UNESCAPED_SLASHES)));
    }
}
