<?php

namespace Inowas\PyprocessingBundle\Tests\Model\Modflow;

use Inowas\PyprocessingBundle\Exception\InvalidArgumentException;
use Inowas\PyprocessingBundle\Model\Modflow\ModflowResultTimeSeriesParameter;

class ModflowResultTimeSeriesInputTest extends \PHPUnit_Framework_TestCase
{
    
    protected $modflowResultTimeSeriesInput;
    
    public function setUp()
    {
        $this->modflowResultTimeSeriesInput = new ModflowResultTimeSeriesParameter(
            'e33b7db0-43a0-43be-a502-fb4f24efd0cc',
            1,
            21,
            20,
            'raw'
        );
    }
    
    public function testSerializeDeserialize(){
        $expectedResult = json_decode('{"model_id":"e33b7db0-43a0-43be-a502-fb4f24efd0cc","time_steps":[1],"stress_periods":[0],"layer":1,"operation":"raw","output_type":"time_series","cell_x":20,"cell_y":21}');
        $this->assertEquals($expectedResult, json_decode(json_encode($this->modflowResultTimeSeriesInput, JSON_UNESCAPED_SLASHES)));
    }

    public function testThrowsExceptionIfOperationNotExists()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $this->modflowResultTimeSeriesInput = new ModflowResultTimeSeriesParameter(
            'e33b7db0-43a0-43be-a502-fb4f24efd0cc',
            1,
            21,
            20,
            'foo'
        );
    }
}


