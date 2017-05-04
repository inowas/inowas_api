<?php

namespace Inowas\ModflowCalculation\Model;

use Inowas\Common\Calculation\ResultType;
use Inowas\Common\DateTime\TotalTime;
use Inowas\Common\Grid\LayerNumber;
use Inowas\Common\Grid\Ncol;
use Inowas\Common\Grid\Nrow;
use Inowas\Common\Id\ModflowId;

class ModflowCalculationReadDataRequest implements \JsonSerializable
{
    const REQUEST_TYPE_LAYER_DATA = "layerdata";
    const REQUEST_TYPE_TIME_SERIES = "timeseries";

    const DATA_TYPE_HEAD = "head";
    const DATA_TYPE_DRAWDOWN = "drawdown";
    const DATA_TYPE_budget = "budget";

    const VERSION = "3.2.6";

    /** @var \stdClass  */
    private $data;

    public static function forLayerData(ModflowId $calculationId, ResultType $dataType, TotalTime $totim, LayerNumber $layer): ModflowCalculationReadDataRequest
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

    public static function forTimeSeries(ModflowId $calculationId, ResultType $dataType, LayerNumber $layer, Nrow $ny, Ncol $nx): ModflowCalculationReadDataRequest
    {
        $arr = array();
        $arr['id'] = $calculationId->toString();
        $arr['type'] = 'flopy_read_data';
        $arr['version'] = self::VERSION;
        $arr['request'] = (object)array(
            self::REQUEST_TYPE_TIME_SERIES => (object)array(
                'type' => $dataType->toString(),
                'layer' => $layer->toInteger(),
                'row' => $ny->toInteger(),
                'column' => $nx->toInteger()
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
