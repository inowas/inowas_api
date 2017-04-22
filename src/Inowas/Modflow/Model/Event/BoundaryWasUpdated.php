<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Common\Boundaries\ModflowBoundary;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\EventSourcing\AggregateChanged;

class BoundaryWasUpdated extends AggregateChanged
{
    /** @var ModflowId */
    private $baseModelId;

    /** @var ModflowBoundary */
    private $boundary;

    /** @var UserId */
    private $userId;

    /** @var  ModflowId */
    private $scenarioId;

    public static function byUserWithBaseModelId(
        UserId $userId,
        ModflowId $baseModelId,
        ModflowBoundary $boundary
    ): BoundaryWasUpdated
    {
        $event = self::occur(
            $baseModelId->toString(), [
                'user_id' => $userId->toString(),
                'basemodel_id' => $baseModelId->toString(),
                'boundary' => serialize($boundary)
            ]
        );

        $event->userId = $userId;
        $event->baseModelId = $baseModelId;
        $event->boundary = $boundary;

        return $event;
    }

    public static function byUserWithBasemodelAndScenarioId(
        UserId $userId,
        ModflowId $baseModelId,
        ModflowId $scenarioId,
        ModflowBoundary $boundary
    ): BoundaryWasUpdated
    {
        $event = self::occur(
            $baseModelId->toString(), [
                'user_id' => $userId->toString(),
                'basemodel_id' => $baseModelId->toString(),
                'scenario_id' => $scenarioId->toString(),
                'boundary' => serialize($boundary)
            ]
        );

        $event->boundary = $boundary;
        $event->baseModelId = $baseModelId;
        $event->scenarioId = $scenarioId;
        $event->userId = $userId;

        return $event;
    }

    public function baseModelId(): ModflowId
    {
        if ($this->baseModelId === null){
            $this->baseModelId = ModflowId::fromString($this->aggregateId());
        }

        return $this->baseModelId;
    }

    public function modelId(): ModflowId
    {
        if ($this->scenarioId === null){
            if (array_key_exists('scenario_id', $this->payload)){
                $this->scenarioId = ModflowId::fromString($this->payload['scenario_id']);
                return $this->scenarioId;
            }
        }

        return $this->baseModelId();
    }

    public function boundary(): ModflowBoundary
    {
        if ($this->boundary === null){
            $this->boundary = unserialize($this->payload['boundary']);
        }

        return $this->boundary;
    }

    public function userId(): UserId
    {
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }
}
