<?php

namespace Tests\AppBundle\Process\Modflow;

use AppBundle\Process\Modflow\ModflowResultRasterInput;

class ModflowResultRasterInputTest extends \PHPUnit_Framework_TestCase
{
    /** @var  ModflowResultRasterInput */
    protected $modflowResultRasterInput;

    public function setUp(){
        $this->modflowResultRasterInput = new ModflowResultRasterInput(
            "e33b7db0-43a0-43be-a502-fb4f24efd0cc",
            1,
            array(0),
            array(0),
            ModflowResultRasterInput::OP_RAW
        );
    }

    public function testSerializeEncodeDecode(){
        $expectedResult = json_decode('{"model_id":"e33b7db0-43a0-43be-a502-fb4f24efd0cc","time_steps":[0],"stress_periods":[0],"layer":1,"operation":"raw","output_type":"raster"}');
        $this->assertEquals($expectedResult, json_decode(json_encode($this->modflowResultRasterInput, JSON_UNESCAPED_SLASHES)));
    }

    public function testThrowsExceptionIfOperationNotExists()
    {
        $this->setExpectedException('AppBundle\Exception\InvalidArgumentException');
        $this->modflowResultRasterInput = new ModflowResultRasterInput(
            "e33b7db0-43a0-43be-a502-fb4f24efd0cc",
            1,
            array(0),
            array(0),
            'foo'
        );
    }
}
