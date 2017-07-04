<?php

declare(strict_types=1);

namespace Inowas\ModflowBoundary\Model\Event;

use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\UserId;
use Prooph\EventSourcing\AggregateChanged;

/** @noinspection LongInheritanceChainInspection */
class BoundaryGeometryWasUpdated extends AggregateChanged
{

    /** @var  BoundaryId */
    private $boundaryId;

    /** @var  Geometry */
    private $geometry;

    /** @var  UserId */
    private $userId;

    public static function of(BoundaryId $boundaryId, UserId $userId, Geometry $geometry): BoundaryGeometryWasUpdated
    {
        $event = self::occur(
            $boundaryId->toString(), [
                'boundary_id' => $boundaryId->toString(),
                'user_id' => $userId->toString(),
                'geometry' => serialize($geometry)
            ]
        );

        $event->boundaryId = $boundaryId;
        $event->geometry = $geometry;

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

    public function geometry(): Geometry
    {
        if ($this->geometry === null){
            $this->geometry = unserialize($this->payload['geometry'], [Geometry::class]);
        }

        return $this->geometry;
    }
}
