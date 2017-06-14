<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Event;

use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\EventSourcing\AggregateChanged;

class BoundaryGeometryWasUpdated extends AggregateChanged
{

    /** @var ModflowId */
    private $modflowModelId;

    /** @var  BoundaryId */
    private $boundaryId;

    /** @var  Geometry */
    private $geometry;

    /** @var  UserId */
    private $userId;

    public static function of(ModflowId $modflowModelId, UserId $userId, BoundaryId $boundaryId, Geometry $geometry): BoundaryGeometryWasUpdated
    {
        $event = self::occur(
            $modflowModelId->toString(), [
                'user_id' => $userId->toString(),
                'boundary_id' => $boundaryId->toString(),
                'geometry' => serialize($geometry)
            ]
        );

        $event->modflowModelId = $modflowModelId;
        $event->boundaryId = $boundaryId;
        $event->geometry = $geometry;

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

    public function geometry(): Geometry
    {
        if ($this->geometry === null){
            $this->geometry = unserialize($this->payload['geometry']);
        }

        return $this->geometry;
    }
}
