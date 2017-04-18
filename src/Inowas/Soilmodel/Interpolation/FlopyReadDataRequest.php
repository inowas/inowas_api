<?php

namespace Inowas\Soilmodel\Interpolation;

use Inowas\Common\Calculation\ResultType;
use Inowas\Common\DateTime\TotalTime;
use Inowas\Common\Grid\LayerNumber;
use Inowas\Common\Id\ModflowId;

class FlopyReadDataRequest implements \JsonSerializable
{
    const REQUEST_TYPE_LAYER_DATA = "layerdata";
    const REQUEST_TYPE_TIME_SERIES = "timeseries";

    const DATA_TYPE_HEAD = "head";
    const DATA_TYPE_DRAWDOWN = "drawdown";
    const DATA_TYPE_budget = "budget";

    const VERSION = "3.2.6";

    /** @var \stdClass  */
    private $data;

    public static function fromLayerdata(ModflowId $calculationId, ResultType $dataType, TotalTime $totim, LayerNumber $layer): FlopyReadDataRequest
    {
        $arr = array();
        $arr['id'] = $calculationId->toString();
        $arr['type'] = 'flopy_read_data';
        $arr['version'] = self::VERSION;
        $arr['request'] = (object)array(
            self::REQUEST_TYPE_LAYER_DATA => (object)array(
                'type' => $dataType->toString(),
                'totim' => $totim->toInteger(),
                'layer' => $layer->toInteger()
            )
        );

        $self = new self();
        $self->data = (object)$arr;
        return $self;
    }

    function jsonSerialize(): \stdClass
    {
        return $this->data;
    }
}
