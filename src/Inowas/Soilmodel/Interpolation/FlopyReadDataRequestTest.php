<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Interpolation;

use Inowas\Common\Id\ModflowId;

class FlopyReadDataRequestTest extends \PHPUnit_Framework_TestCase
{

    public function test_serialize_flopy_read_data_request(): void
    {
        $id = ModflowId::fromString('c561c4e5-7915-426a-ba2b-0c4badeae9c3');
        $dataType = FlopyReadDataRequest::DATA_TYPE_HEAD;
        $totim = 1095.0;
        $layer = 3;
        $obj = FlopyReadDataRequest::fromLayerdata($id, $dataType, $totim, $layer);
        $this->assertEquals('{"id":"c561c4e5-7915-426a-ba2b-0c4badeae9c3","type":"layerdata","version":"3.2.6","request":{"layerdata":{"type":"head","totim":1095,"layer":3}}}', json_encode($obj));
    }
}
