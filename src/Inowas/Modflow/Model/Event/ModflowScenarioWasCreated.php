<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Modflow\Model\ModflowModelId;
use Inowas\Modflow\Model\ScenarioId;
use Prooph\EventSourcing\AggregateChanged;

class ModflowScenarioWasCreated extends AggregateChanged
{

    /** @var  ScenarioId */
    private $scenarioId;

    /** @var  ModflowModelId */
    private $modflowModelId;

    public static function withId(ModflowModelId $modflowModelId, ScenarioId $scenarioId): ModflowScenarioWasCreated
    {
        $event = self::occur($modflowModelId->toString(), [
            'scenario_id' => $scenarioId->toString()
        ]);

        $event->scenarioId = $scenarioId;
        $event->modflowModelId = $modflowModelId;

        return $event;
    }

    public function scenarioId(): ScenarioId
    {
        if ($this->scenarioId === null){
            $this->scenarioId = ScenarioId::fromString($this->payload['scenario_id']);
        }

        return $this->scenarioId;
    }

    public function modflowModelId(): ModflowModelId
    {
        if ($this->modflowModelId === null){
            $this->modflowModelId = ModflowModelId::fromString($this->aggregateId());
        }

        return $this->modflowModelId;
    }
}
