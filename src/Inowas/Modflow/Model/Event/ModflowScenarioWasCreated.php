<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Modflow\Model\ModflowModelId;
use Inowas\Modflow\Model\ScenarioId;
use Inowas\Modflow\Model\UserId;
use Prooph\EventSourcing\AggregateChanged;

class ModflowScenarioWasCreated extends AggregateChanged
{

    /** @var  ScenarioId */
    private $scenarioId;

    /** @var  ModflowModelId */
    private $modflowModelId;

    /** @var  UserId */
    protected $userId;

    public static function withId(UserId $userId, ModflowModelId $modflowModelId, ScenarioId $scenarioId): ModflowScenarioWasCreated
    {
        $event = self::occur($modflowModelId->toString(), [
            'scenario_id' => $scenarioId->toString(),
            'user_id' => $userId->toString()
        ]);

        $event->scenarioId = $scenarioId;
        $event->modflowModelId = $modflowModelId;
        $event->userId = $userId;

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

    public function userId(): UserId
    {
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }
}
