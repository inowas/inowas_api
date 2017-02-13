<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Modflow\Model\BoundaryId;
use Inowas\Modflow\Model\ModflowBoundary;
use Inowas\Modflow\Model\ModflowModelId;
use Inowas\Modflow\Model\ScenarioId;
use Prooph\EventSourcing\AggregateChanged;

class ModflowScenarioBoundaryWasRemoved extends AggregateChanged
{

    /** @var  ModflowModelId */
    private $modflowModelId;

    /** @var ModflowBoundary */
    private $boundaryId;

    /** @var ScenarioId */
    private $scenarioId;

    public static function fromScenarioWithBoundary(
        ModflowModelId $modflowModelId,
        ScenarioId $scenarioId,
        BoundaryId $boundaryId
    ): ModflowScenarioBoundaryWasRemoved
    {
        $event = self::occur(
            $modflowModelId->toString(), [
                'scenario_id' => $scenarioId->toString(),
                'boundary_id' => $boundaryId->toString()
            ]
        );

        $event->modflowModelId = $modflowModelId;
        $event->boundaryId = $boundaryId;
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

    public function boundaryId(): BoundaryId
    {
        if ($this->boundaryId === null){
            $this->boundaryId = BoundaryId::fromString($this->payload['boundary_id']);
        }

        return $this->boundaryId;
    }

    public function scenarioId(): ScenarioId
    {
        if ($this->scenarioId === null) {
            $this->scenarioId = ScenarioId::fromString($this->payload['scenario_id']);
        }

        return $this->scenarioId;
    }
}
