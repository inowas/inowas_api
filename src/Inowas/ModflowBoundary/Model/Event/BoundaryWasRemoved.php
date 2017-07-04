<?php

declare(strict_types=1);

namespace Inowas\ModflowBoundary\Model\Event;

use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\EventSourcing\AggregateChanged;

/** @noinspection LongInheritanceChainInspection */
class BoundaryWasRemoved extends AggregateChanged
{

    /** @var BoundaryId */
    private $boundaryId;

    /** @var ModflowId */
    private $modflowModelId;

    /** @var UserId */
    private $userId;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param BoundaryId $boundaryId
     * @param ModflowId $modelId
     * @param UserId $userId
     * @return BoundaryWasRemoved
     */
    public static function fromModelWithId(
        BoundaryId $boundaryId,
        ModflowId $modelId,
        UserId $userId
    ): BoundaryWasRemoved
    {

        $event = self::occur($boundaryId->toString(),[
            'model_id' => $modelId->toString(),
            'user_id' => $userId->toString()
        ]);


        $event->boundaryId = $boundaryId;
        $event->modflowModelId = $modelId;
        $event->userId = $userId;
        return $event;
    }

    public function boundaryId(): BoundaryId
    {
        if (null === $this->boundaryId) {
            $this->boundaryId = BoundaryId::fromString($this->aggregateId());
        }

        return $this->boundaryId;
    }

    public function modelId(): ModflowId
    {
        if (null === $this->modflowModelId) {
            $this->modflowModelId = ModflowId::fromString($this->payload['model_id']);
        }

        return $this->modflowModelId;
    }

    public function userId(): UserId
    {
        if (null === $this->userId) {
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }
}
