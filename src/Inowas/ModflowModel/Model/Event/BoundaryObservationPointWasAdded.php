<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Event;

use Inowas\Common\Boundaries\ObservationPoint;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\EventSourcing\AggregateChanged;

class BoundaryObservationPointWasAdded extends AggregateChanged
{

    /** @var ModflowId */
    private $modflowId;

    /** @var UserId */
    private $userId;

    /** @var BoundaryId */
    private $boundaryId;

    /** @var ObservationPoint */
    private $observationPoint;

    public static function byUserWithModflowAndBoundaryId(
        UserId $userId,
        ModflowId $modflowId,
        BoundaryId $boundaryId,
        ObservationPoint $observationPoint
    ): BoundaryObservationPointWasAdded
    {
        $event = self::occur(
            $modflowId->toString(), [
                'user_id' => $userId->toString(),
                'boundary_id' => $boundaryId->toString(),
                'observationpoint' => serialize($observationPoint)
            ]
        );

        $event->modflowId = $modflowId;
        $event->boundaryId = $boundaryId;
        $event->observationPoint = $observationPoint;

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

    public function observationPoint(): ObservationPoint
    {
        if ($this->observationPoint === null){
            $this->observationPoint = unserialize($this->payload['observationpoint']);
        }

        return $this->observationPoint();
    }
}
