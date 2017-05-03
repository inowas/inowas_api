<?php

declare(strict_types=1);

namespace Inowas\ModflowCalculation\Model\Command;

use Inowas\Common\Id\ModflowId;
use Inowas\ModflowCalculation\Model\ModflowCalculationResponse;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class UpdateCalculationResults extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function withResponse(
        ModflowId $calculationId,
        ModflowCalculationResponse $response
    ): UpdateCalculationResults
    {
        return new self(
            [
                'calculation_id' => $calculationId->toString(),
                'response' => $response->toArray()
            ]
        );
    }

    public function calculationId(): ModflowId
    {
        return ModflowId::fromString($this->payload['calculation_id']);
    }

    public function response(): ModflowCalculationResponse
    {
        return ModflowCalculationResponse::fromArray($this->payload['response']);
    }
}
