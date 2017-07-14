<?php

declare(strict_types=1);

namespace Inowas\ModflowBoundary\Model\Event;

use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\Name;
use Prooph\EventSourcing\AggregateChanged;

/** @noinspection LongInheritanceChainInspection */
class BoundaryNameWasUpdated extends AggregateChanged
{

    /** @var UserId */
    private $userId;

    /** @var BoundaryId */
    private $boundaryId;

    /** @var Name */
    private $boundaryName;

    public static function of(BoundaryId $boundaryId, UserId $userId, Name $boundaryName): BoundaryNameWasUpdated
    {
        $event = self::occur(
            $boundaryId->toString(), [
                'user_id' => $userId->toString(),
                'boundary_name' => $boundaryName
            ]
        );

        $event->boundaryId = $boundaryId;
        $event->boundaryName = $boundaryName;

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

    public function boundaryName(): Name
    {
        if ($this->boundaryName === null){
            $this->boundaryName = Name::fromString($this->payload['boundary_name']);
        }

        return $this->boundaryName;
    }
}
