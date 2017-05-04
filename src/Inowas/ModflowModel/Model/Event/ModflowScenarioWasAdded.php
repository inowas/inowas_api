<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Event;

use Inowas\Common\Id\IdInterface;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\EventSourcing\AggregateChanged;

class ModflowScenarioWasAdded extends AggregateChanged
{

    /** @var  ModflowId */
    private $scenarioId;

    /** @var  ModflowId */
    private $baseModelId;

    /** @var  UserId */
    protected $userId;

    public static function withId(UserId $userId, IdInterface $baseModelId, IdInterface $scenarioId): ModflowScenarioWasAdded
    {
        $event = self::occur($baseModelId->toString(), [
            'basemodel_id' => $baseModelId->toString(),
            'scenario_id' => $scenarioId->toString(),
            'user_id' => $userId->toString()
        ]);

        $event->scenarioId = $scenarioId;
        $event->baseModelId = $baseModelId;
        $event->userId = $userId;

        return $event;
    }

    public function scenarioId(): ModflowId
    {
        if ($this->scenarioId === null){
            $this->scenarioId = ModflowId::fromString($this->payload['scenario_id']);
        }

        return $this->scenarioId;
    }

    public function baseModelId(): ModflowId
    {
        if ($this->baseModelId === null){
            $this->baseModelId = ModflowId::fromString($this->aggregateId());
        }

        return $this->baseModelId;
    }

    public function userId(): UserId
    {
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }
}
