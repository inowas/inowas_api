<?php

declare(strict_types=1);

namespace tests\Inowas\ModflowCalculation\Model;

use Inowas\ModflowCalculation\Model\ModflowCalculationReadDataRequest;
use Inowas\Common\Calculation\ResultType;
use Inowas\Common\DateTime\TotalTime;
use Inowas\Common\Grid\LayerNumber;
use Inowas\Common\Id\ModflowId;

class FlopyCalculationReadDataRequestTest extends \PHPUnit_Framework_TestCase
{

    public function test_serialize_flopy_read_data_request(): void
    {
        $id = ModflowId::fromString('c561c4e5-7915-426a-ba2b-0c4badeae9c3');
        $obj = ModflowCalculationReadDataRequest::fromLayerdata($id, ResultType::fromString(ResultType::HEAD_TYPE), TotalTime::fromInt(1095), LayerNumber::fromInteger(3));
        $this->assertEquals('{"id":"c561c4e5-7915-426a-ba2b-0c4badeae9c3","type":"flopy_read_data","version":"3.2.6","request":{"layerdata":{"type":"head","totim":1095,"layer":3}}}', json_encode($obj));
    }
}
