<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\EventSourcing\AggregateChanged;

class BoundaryWasRemovedFromScenario extends AggregateChanged
{

    /** @var \Inowas\Common\Id\ModflowId */
    private $modflowId;

    /** @var \Inowas\Common\Id\ModflowId */
    private $scenarioId;

    /** @var \Inowas\Common\Id\BoundaryId */
    private $boundaryId;

    /** @var UserId */
    private $userId;


    public static function fromScenario(
        UserId $userId,
        ModflowId $modflowId,
        ModflowId $scenarioId,
        BoundaryId $boundaryId
    ): BoundaryWasRemovedFromScenario
    {
        $event = self::occur(
            $modflowId->toString(), [
                'user_id' => $userId->toString(),
                'scenario_id' => $scenarioId->toString(),
                'boundary_id' => $boundaryId->toString()
            ]
        );

        $event->userId = $userId;
        $event->modflowId = $modflowId;
        $event->scenarioId = $scenarioId;
        $event->boundaryId = $boundaryId;

        return $event;
    }

    public function modflowId(): ModflowId
    {
        if ($this->modflowId === null){
            $this->modflowId = ModflowId::fromString($this->aggregateId());
        }

        return $this->modflowId;
    }

    public function boundaryId(): BoundaryId
    {
        if ($this->boundaryId === null){
            $this->boundaryId = BoundaryId::fromString($this->payload['boundary_id']);
        }

        return $this->boundaryId;
    }

    public function userId(): UserId
    {
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }

    public function scenarioId(): ModflowId
    {
        if ($this->scenarioId === null){
            $this->scenarioId = ModflowId::fromString($this->payload['scenario_id']);
        }

        return $this->scenarioId;
    }
}
