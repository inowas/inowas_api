<?php

declare(strict_types=1);

namespace Inowas\ModflowCalculation\Model;

class ModflowCalculationReadDataResponse
{
    const REQUEST_TYPE_LAYER_DATA = "layerdata";
    const REQUEST_TYPE_TIME_SERIES = "timeseries";

    const DATA_TYPE_HEAD = "head";
    const DATA_TYPE_DRAWDOWN = "drawdown";
    const DATA_TYPE_budget = "budget";

    const VERSION = "3.2.6";

    protected $data = [];

    public static function fromJson(string $json): ModflowCalculationReadDataResponse
    {
        $obj = json_decode($json);
        $self = new self();
        $self->data = $obj->response;
        return $self;
    }

    public function data(): array
    {
        return $this->data;
    }
}
