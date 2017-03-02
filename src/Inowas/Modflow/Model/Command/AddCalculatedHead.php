<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Command;

use Inowas\Common\LayerNumber;
use Inowas\Modflow\Model\HeadData;
use Inowas\Modflow\Model\ResultType;
use Inowas\Modflow\Model\ModflowId;
use Inowas\Modflow\Model\TotalTime;
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
