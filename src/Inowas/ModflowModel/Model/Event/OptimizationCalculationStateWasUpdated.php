<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Event;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Modflow\OptimizationState;
use Prooph\EventSourcing\AggregateChanged;

class OptimizationCalculationStateWasUpdated extends AggregateChanged
{
    /** @var ModflowId */
    private $modflowId;

    /** @var ModflowId */
    private $optimizationId;

    /** @var  OptimizationState */
    private $state;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param ModflowId $modflowId
     * @param ModflowId $optimizationId
     * @param OptimizationState $state
     * @return self
     */
    public static function byModel(ModflowId $modflowId, ModflowId $optimizationId, OptimizationState $state): self
    {
        /** @var self $event */
        $event = self::occur(
            $modflowId->toString(), [
                'optimization_id' => $optimizationId->toString(),
                'state' => $state->toInt()
            ]
        );

        $event->modflowId = $modflowId;
        $event->state = $state;
        $event->optimizationId = $optimizationId;

        return $event;
    }

    public function modelId(): ModflowId
    {
        if ($this->modflowId === null) {
            $this->modflowId = ModflowId::fromString($this->aggregateId());
        }

        return $this->modflowId;
    }

    public function optimizationId(): ModflowId
    {
        if ($this->optimizationId === null) {
            $this->optimizationId = ModflowId::fromString($this->payload['optimization_id']);
        }

        return $this->optimizationId;
    }

    public function state(): OptimizationState
    {
        if ($this->state === null) {
            $this->state = OptimizationState::fromInt($this->payload['state']);
        }

        return $this->state;
    }
}
