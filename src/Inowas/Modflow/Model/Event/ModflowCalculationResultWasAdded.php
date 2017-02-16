<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Modflow\Model\CalculationResult;
use Inowas\Modflow\Model\ModflowId;
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
            'result' => serialize($result)
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
            $this->result = unserialize($this->payload['result']);
        }

        return $this->result;
    }
}
