<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Event;

use Inowas\Common\Boundaries\ModflowBoundary;
use Inowas\Common\Boundaries\ObservationPoint;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\EventSourcing\AggregateChanged;

class ObservationPointWasAdded extends AggregateChanged
{

    /** @var ModflowId */
    private $modflowId;

    /** @var ModflowBoundary */
    private $boundary;

    /** @var UserId */
    private $userId;

    public static function to(
        ModflowId $modflowId,
        UserId $userId,
        BoundaryId $boundaryId,
        ObservationPoint $observationPoint
    ): ObservationPointWasAdded
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
        $event->onservationPoint = $observationPoint;

        return $event;
    }

    public function modflowId(): ModflowId
    {
        if ($this->modflowId === null){
            $this->modflowId = ModflowId::fromString($this->aggregateId());
        }

        return $this->modflowId;
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
