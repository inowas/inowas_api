<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Event;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Modflow\OptimizationProgress;
use Inowas\Common\Modflow\OptimizationSolutions;
use Inowas\Common\Modflow\OptimizationState;
use Prooph\EventSourcing\AggregateChanged;

class OptimizationResultsWereUpdated extends AggregateChanged
{

    /** @var ModflowId */
    private $modflowId;

    /** @var  ModflowId */
    private $optimizationId;

    /** @var OptimizationProgress */
    private $progress;

    /** @var OptimizationSolutions */
    private $solutions;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param ModflowId $modflowId
     * @param ModflowId $optimizationId
     * @param OptimizationProgress $progress
     * @param OptimizationSolutions $solutions
     * @return self
     */
    public static function byModel(
        ModflowId $modflowId,
        ModflowId $optimizationId,
        OptimizationProgress $progress,
        OptimizationSolutions $solutions
    ): self
    {
        /** @var self $event */
        $event = self::occur(
            $modflowId->toString(), [
                'optimization_id' => $optimizationId->toString(),
                'progress' => $progress->toArray(),
                'solutions' => $solutions->toArray()
            ]
        );

        $event->modflowId = $modflowId;
        $event->optimizationId = $optimizationId;
        $event->progress = $progress;
        $event->solutions = $solutions;

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

    public function progress(): OptimizationProgress
    {
        if ($this->progress === null) {
            $this->progress = OptimizationProgress::fromArray($this->payload['progress']);
        }

        return $this->progress;
    }

    public function solutions(): OptimizationSolutions
    {
        if ($this->solutions === null) {
            $this->solutions = OptimizationSolutions::fromArray($this->payload['solutions']);
        }

        return $this->solutions;
    }

    public function state(): OptimizationState
    {
        if ($this->progress->finished()) {
            return OptimizationState::fromInt(OptimizationState::FINISHED);
        }

        return OptimizationState::fromInt(OptimizationState::CALCULATING);
    }
}
