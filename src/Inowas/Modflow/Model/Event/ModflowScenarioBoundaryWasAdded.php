<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Modflow\Model\ModflowBoundary;
use Inowas\Modflow\Model\ModflowModelId;
use Inowas\Modflow\Model\ScenarioId;
use Prooph\EventSourcing\AggregateChanged;

class ModflowScenarioBoundaryWasAdded extends AggregateChanged
{

    /** @var  ModflowModelId */
    private $modflowModelId;

    /** @var ModflowBoundary */
    private $boundary;

    /** @var ScenarioId */
    private $scenarioId;

    public static function toScenarioWithBoundary(
        ModflowModelId $modflowModelId,
        ScenarioId $scenarioId,
        ModflowBoundary $boundary
    ): ModflowScenarioBoundaryWasAdded
    {
        $event = self::occur(
            $modflowModelId->toString(), [
                'scenario_id' => $scenarioId->toString(),
                'boundary' => serialize($boundary)
            ]
        );

        $event->modflowModelId = $modflowModelId;
        $event->boundary = $boundary;
        $event->scenarioId = $scenarioId;

        return $event;
    }

    public function modflowModelId(): ModflowModelId
    {
        if ($this->modflowModelId === null){
            $this->modflowModelId = ModflowModelId::fromString($this->aggregateId());
        }

        return $this->modflowModelId;
    }

    public function boundary(): ModflowBoundary
    {
        if ($this->boundary === null){
            $this->boundary = unserialize($this->payload['boundary']);
        }

        return $this->boundary;
    }


    public function scenarioId(): ScenarioId
    {
        if ($this->scenarioId === null) {
            $this->scenarioId = ScenarioId::fromString($this->payload['scenario_id']);
        }

        return $this->scenarioId;
    }
}
