<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Event;

use Inowas\Common\Boundaries\BoundaryName;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\EventSourcing\AggregateChanged;

class BoundaryNameWasUpdated extends AggregateChanged
{

    /** @var ModflowId */
    private $modflowModelId;

    /** @var UserId */
    private $userId;

    /** @var BoundaryId */
    private $boundaryId;

    /** @var BoundaryName */
    private $boundaryName;

    public static function of(ModflowId $modflowModelId, UserId $userId, BoundaryId $boundaryId, BoundaryName $boundaryName): BoundaryNameWasUpdated
    {
        $event = self::occur(
            $modflowModelId->toString(), [
                'user_id' => $userId->toString(),
                'boundary_id' => $boundaryId->toString(),
                'boundary_name' => $boundaryName
            ]
        );

        $event->modflowModelId = $modflowModelId;
        $event->boundaryId = $boundaryId;
        $event->boundaryName = $boundaryName;

        return $event;
    }

    public function modflowModelId(): ModflowId
    {
        if ($this->modflowModelId === null){
            $this->modflowModelId = ModflowId::fromString($this->aggregateId());
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

    public function userId(): UserId
    {
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }

    public function boundaryName(): BoundaryName
    {
        if ($this->boundaryName === null){
            $this->boundaryName = BoundaryName::fromString($this->payload['boundary_name']);
        }

        return $this->boundaryName;
    }
}
