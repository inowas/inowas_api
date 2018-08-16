<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Event;

use Inowas\Common\Calculation\CalculationState;
use Inowas\Common\Id\CalculationId;
use Inowas\Common\Id\ModflowId;
use Prooph\EventSourcing\AggregateChanged;

/** @noinspection LongInheritanceChainInspection */

class PreProcessingWasFinished extends AggregateChanged
{
    /** @var  ModflowId */
    private $modelId;

    /** @var  CalculationId */
    private $calculationId;

    public static function withId(ModflowId $modflowId, CalculationId $calculationId): self
    {
        /** @var self $event */
        $event = self::occur(
            $modflowId->toString(), [
                'calculation_id' => $calculationId->toString()
            ]
        );

        return $event;
    }

    public function calculationId(): CalculationId
    {
        if ($this->calculationId === null) {
            $this->calculationId = CalculationId::fromString($this->payload['calculation_id']);
        }

        return $this->calculationId;
    }

    public function modelId(): ModflowId
    {
        if ($this->modelId === null) {
            $this->modelId = ModflowId::fromString($this->aggregateId());
        }

        return $this->modelId;
    }

    public function state(): CalculationState
    {
        return CalculationState::preprocessingFinished();
    }
}
