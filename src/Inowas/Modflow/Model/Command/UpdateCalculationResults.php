<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Command;

use Inowas\Common\Id\ModflowId;
use Inowas\Soilmodel\Interpolation\FlopyCalculationResponse;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class UpdateCalculationResults extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function withResponse(
        ModflowId $calculationId,
        FlopyCalculationResponse $response
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

    public function response(): FlopyCalculationResponse
    {
        return FlopyCalculationResponse::fromArray($this->payload['response']);
    }
}
