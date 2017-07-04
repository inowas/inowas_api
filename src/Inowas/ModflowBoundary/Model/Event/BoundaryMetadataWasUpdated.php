<?php

declare(strict_types=1);

namespace Inowas\ModflowBoundary\Model\Event;

use Inowas\Common\Boundaries\BoundaryMetadata;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\UserId;
use Prooph\EventSourcing\AggregateChanged;

/** @noinspection LongInheritanceChainInspection */
class BoundaryMetadataWasUpdated extends AggregateChanged
{

    /** @var UserId */
    private $userId;

    /** @var BoundaryId */
    private $boundaryId;

    /** @var BoundaryMetadata */
    private $boundaryMetadata;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param UserId $userId
     * @param BoundaryId $boundaryId
     * @param BoundaryMetadata $metadata
     * @return BoundaryMetadataWasUpdated
     */
    public static function of(BoundaryId $boundaryId, UserId $userId, BoundaryMetadata $metadata): BoundaryMetadataWasUpdated
    {
        $event = self::occur(
            $boundaryId->toString(), [
                'user_id' => $userId->toString(),
                'boundary_id' => $boundaryId->toString(),
                'metadata' => $metadata->toArray()
            ]
        );

        $event->boundaryId = $boundaryId;
        $event->boundaryMetadata = $metadata;

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

    public function metadata(): BoundaryMetadata
    {
        if ($this->boundaryMetadata === null){
            $this->boundaryMetadata = BoundaryMetadata::fromArray($this->payload['metadata']);
        }

        return $this->boundaryMetadata;
    }
}
