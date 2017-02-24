<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Command;

use Inowas\Modflow\Model\CalculationResultWithData;
use Inowas\Modflow\Model\CalculationResultData;
use Inowas\Modflow\Model\CalculationResultType;
use Inowas\Modflow\Model\LayerNumber;
use Inowas\Modflow\Model\ModflowId;
use Inowas\Modflow\Model\TotalTime;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class AddResultToCalculation extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function to(ModflowId $calculationId, CalculationResultWithData $result): AddResultToCalculation
    {
        $payload = [
            'calculation_id' => $calculationId->toString(),
            'totim' => $result->totalTime()->toInteger(),
            'type' => $result->type()->toString(),
            'data' => $result->data()->toArray(),
            'layer' => $result->layerNumber()->toInteger()
        ];

        return new self($payload);
    }

    public function calculationId(): ModflowId
    {
        return ModflowId::fromString($this->payload['calculation_id']);
    }

    public function result(): CalculationResultWithData
    {
        return CalculationResultWithData::fromParameters(
            CalculationResultType::fromString($this->payload['type']),
            TotalTime::fromInt($this->payload['totim']),
            LayerNumber::fromInteger($this->payload['layer']),
            CalculationResultData::from2dArray($this->payload['data'])
        );
    }
}
