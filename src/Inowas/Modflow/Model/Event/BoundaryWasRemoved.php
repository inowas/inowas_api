<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Modflow\Model\BoundaryId;
use Inowas\Modflow\Model\ModflowId;
use Inowas\Modflow\Model\UserId;
use Prooph\EventSourcing\AggregateChanged;

class BoundaryWasRemoved extends AggregateChanged
{

    /** @var  ModflowId */
    private $modflowId;

    /** @var BoundaryId */
    private $boundaryId;

    /** @var UserId */
    private $userId;

    public static function withBoundaryId(UserId $userId, ModflowId $modflowModelId, BoundaryId $boundaryId): BoundaryWasRemoved
    {
        $event = self::occur(
            $modflowModelId->toString(), [
                'user_id' => $userId->toString(),
                'boundary_id' => $boundaryId->toString()
            ]
        );

        $event->modflowId = $modflowModelId;
        $event->boundaryId = $boundaryId;
        $event->userId = $userId;

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
}
