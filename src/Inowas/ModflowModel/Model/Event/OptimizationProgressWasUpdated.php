<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Event;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\OptimizationProgress;
use Prooph\EventSourcing\AggregateChanged;

/** @noinspection LongInheritanceChainInspection */
class OptimizationProgressWasUpdated extends AggregateChanged
{

    /** @var ModflowId */
    private $modflowId;

    /** @var  UserId */
    private $userId;

    /** @var OptimizationProgress */
    private $progress;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param UserId $userId
     * @param ModflowId $modflowId
     * @param OptimizationProgress $progress
     * @return self
     */
    public static function byUserToModel(UserId $userId, ModflowId $modflowId, OptimizationProgress $progress): self
    {
        /** @var self $event */
        $event = self::occur(
            $modflowId->toString(), [
                'user_id' => $userId->toString(),
                'progress' => $progress->toArray()
            ]
        );

        $event->modflowId = $modflowId;
        $event->userId = $userId;
        $event->progress = $progress;

        return $event;
    }

    public function modelId(): ModflowId
    {
        if ($this->modflowId === null) {
            $this->modflowId = ModflowId::fromString($this->aggregateId());
        }

        return $this->modflowId;
    }

    public function progress(): OptimizationProgress
    {
        if ($this->progress === null) {
            $this->progress = OptimizationProgress::fromArray($this->payload['progress']);
        }

        return $this->progress;
    }

    public function userId(): UserId
    {
        if ($this->userId === null) {
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }
}
