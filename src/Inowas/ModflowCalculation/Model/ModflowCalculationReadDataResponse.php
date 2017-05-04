<?php

declare(strict_types=1);

namespace Inowas\ModflowCalculation\Model;

use Inowas\Common\Status\StatusCode;

class ModflowCalculationReadDataResponse
{
    const REQUEST_TYPE_LAYER_DATA = "layerdata";
    const REQUEST_TYPE_TIME_SERIES = "timeseries";

    const DATA_TYPE_HEAD = "head";
    const DATA_TYPE_DRAWDOWN = "drawdown";
    const DATA_TYPE_budget = "budget";

    const VERSION = "3.2.6";

    protected $data = [];

    /** @var  StatusCode */
    protected $statusCode;

    public static function fromJson(string $json): ModflowCalculationReadDataResponse
    {
        $obj = json_decode($json);
        $self = new self();
        $self->statusCode = StatusCode::fromInt((int)$obj->status_code);

        $timeSeries = [];
        $data = $obj->response;
        foreach ($data as $dataSet){
            $key = (int)$dataSet[0];
            $value = (float)$dataSet[1];
            $timeSeries[$key] = $value;
        }

        $self->data = $timeSeries;
        return $self;
    }

    public function data(): array
    {
        return $this->data;
    }

    public function statusCode(): StatusCode
    {
        return $this->statusCode;
    }
}
