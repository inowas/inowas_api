<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Modflow\Model\CalculationResult;
use Inowas\Modflow\Model\CalculationResultData;
use Inowas\Modflow\Model\CalculationResultType;
use Inowas\Modflow\Model\ModflowId;
use Inowas\Modflow\Model\TotalTime;
use Prooph\EventSourcing\AggregateChanged;

class ModflowCalculationResultWasAdded extends AggregateChanged
{
    /** @var  ModflowId */
    private $calculationId;

    /** @var  CalculationResult */
    private $result;

    public static function to(ModflowId $calculationId, CalculationResult $result): ModflowCalculationResultWasAdded
    {
        $event = self::occur($calculationId->toString(),[
            'total_time' => $result->totalTime()->toTotalTime(),
            'type' => $result->type()->toString(),
            'data' => $result->data()->toArray()
        ]);

        $event->result = $result;
        return $event;
    }

    public function calculationId(): ModflowId
    {
        if ($this->calculationId === null){
            $this->calculationId = ModflowId::fromString($this->aggregateId());
        }

        return $this->calculationId;
    }

    public function result(): CalculationResult
    {
        if ($this->result === null){

            $this->result = CalculationResult::fromParameters(
                TotalTime::fromInt($this->payload['total_time']),
                CalculationResultType::fromString($this->payload['type']),
                CalculationResultData::from3dArray($this->payload['data'])
            );
        }

        return $this->result;
    }
}
