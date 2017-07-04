<?php

declare(strict_types=1);

namespace Inowas\ModflowBoundary\Model\Event;

use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ObservationPointId;
use Inowas\Common\Id\UserId;
use Prooph\EventSourcing\AggregateChanged;

/** @noinspection LongInheritanceChainInspection */
class BoundaryObservationPointWasRemoved extends AggregateChanged
{

    /** @var UserId */
    private $userId;

    /** @var BoundaryId */
    private $boundaryId;

    /** @var ObservationPointId */
    private $observationPointId;


    public static function byUserWithId(
        BoundaryId $boundaryId,
        UserId $userId,
        ObservationPointId $observationPointId
    ): BoundaryObservationPointWasRemoved
    {
        $event = self::occur(
            $boundaryId->toString(), [
                'user_id' => $userId->toString(),
                'boundary_id' => $boundaryId->toString(),
                'observationpoint_id' => $observationPointId->toString()
            ]
        );

        $event->boundaryId = $boundaryId;
        $event->observationPointId = $observationPointId;
        $event->userId = $userId;

        return $event;
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

    public function observationPointId(): ObservationPointId
    {
        if ($this->observationPointId === null){
            $this->observationPointId = ObservationPointId::fromString($this->payload['observationpoint_id']);
        }

        return $this->observationPointId;
    }
}
