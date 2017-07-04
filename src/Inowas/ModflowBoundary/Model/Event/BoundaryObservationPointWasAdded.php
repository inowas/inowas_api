<?php

declare(strict_types=1);

namespace Inowas\ModflowBoundary\Model\Event;

use Inowas\Common\Boundaries\ObservationPoint;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\UserId;
use Prooph\EventSourcing\AggregateChanged;

/** @noinspection LongInheritanceChainInspection */
class BoundaryObservationPointWasAdded extends AggregateChanged
{
    /** @var UserId */
    private $userId;

    /** @var BoundaryId */
    private $boundaryId;

    /** @var ObservationPoint */
    private $observationPoint;

    public static function addedByUserWithData(
        BoundaryId $boundaryId,
        UserId $userId,
        ObservationPoint $observationPoint
    ): BoundaryObservationPointWasAdded
    {
        $event = self::occur(
            $boundaryId->toString(), [
                'user_id' => $userId->toString(),
                'boundary_id' => $boundaryId->toString(),
                'observationpoint' => serialize($observationPoint)
            ]
        );

        $event->boundaryId = $boundaryId;
        $event->observationPoint = $observationPoint;

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

    public function observationPoint(): ObservationPoint
    {
        if ($this->observationPoint === null){
            $this->observationPoint = unserialize($this->payload['observationpoint'], [ObservationPoint::class]);
        }

        return $this->observationPoint;
    }
}
