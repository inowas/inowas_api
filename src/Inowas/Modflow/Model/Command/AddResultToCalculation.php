<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Command;

use Inowas\Modflow\Model\CalculationResult;
use Inowas\Modflow\Model\ModflowId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class AddResultToCalculation extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function to(ModflowId $calculationId, CalculationResult $result): AddResultToCalculation
    {
        $payload = [
            'calculation_id' => $calculationId->toString(),
            'result' => serialize($result)
        ];

        return new self($payload);
    }

    public function calculationId(): ModflowId
    {
        return ModflowId::fromString($this->payload['calculation_id']);
    }

    public function result(): CalculationResult
    {
        return unserialize($this->payload['result']);
    }
}
