<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Command;

use Inowas\Common\Calculation\Budget;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\DateTime\TotalTime;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class AddCalculatedBudget extends Command implements PayloadConstructable
{
    use PayloadTrait;

    public static function to(ModflowId $calculationId, TotalTime $totalTime, Budget $budgetData): AddCalculatedBudget
    {
        $payload = [
            'calculation_id' => $calculationId->toString(),
            'totim' => $totalTime->toInteger(),
            'data' => $budgetData->toArray()
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

    public function budget(): Budget
    {
        return Budget::fromArray($this->payload['data']);
    }
}
