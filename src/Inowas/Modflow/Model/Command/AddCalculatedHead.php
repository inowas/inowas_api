<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Command;

use Inowas\Common\Grid\LayerNumber;
use Inowas\Common\Calculation\HeadData;
use Inowas\Common\Calculation\ResultType;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\DateTime\TotalTime;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class AddCalculatedHead extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function to(
        ModflowId $calculationId,
        TotalTime $totalTime,
        ResultType $resultType,
        HeadData $headData,
        LayerNumber $layerNumber
    ): AddCalculatedHead
    {
        $payload = [
            'calculation_id' => $calculationId->toString(),
            'totim' => $totalTime->toInteger(),
            'type' => $resultType->toString(),
            'data' => $headData->toArray(),
            'layer' => $layerNumber->toInteger()
        ];

        return new self($payload);
    }

    public function calculationId(): ModflowId
    {
        return ModflowId::fromString($this->payload['calculation_id']);
    }

    public function totalTime(): TotalTime
    {
        return TotalTime::fromInt($this->payload['totim']);
    }

    public function type(): ResultType
    {
        return ResultType::fromString($this->payload['type']);
    }

    public function layerNumber(): LayerNumber
    {
        return LayerNumber::fromInteger($this->payload['layer']);
    }

    public function data(): HeadData
    {
        return HeadData::from2dArray($this->payload['data']);
    }
}
