<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Event;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\EventSourcing\AggregateChanged;

/** @noinspection LongInheritanceChainInspection */

class OptimizationCalculationWasStarted extends AggregateChanged
{

    /** @var ModflowId */
    private $modflowId;

    /** @var  UserId */
    private $userId;

    /** @var ModflowId */
    private $optimizationId;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param UserId $userId
     * @param ModflowId $modflowId
     * @param ModflowId $optimizationId
     * @return self
     */
    public static function byUserToModel(UserId $userId, ModflowId $modflowId, ModflowId $optimizationId): self
    {
        /** @var self $event */
        $event = self::occur(
            $modflowId->toString(), [
                'user_id' => $userId->toString(),
                'optimization_id' => $optimizationId->toString()
            ]
        );

        $event->modflowId = $modflowId;
        $event->userId = $userId;
        $event->optimizationId = $optimizationId;

        return $event;
    }

    public function modelId(): ModflowId
    {
        if ($this->modflowId === null) {
            $this->modflowId = ModflowId::fromString($this->aggregateId());
        }

        return $this->modflowId;
    }

    public function optimizationId(): ModflowId
    {
        if ($this->optimizationId === null) {
            $this->optimizationId = ModflowId::fromString($this->payload['optimization_id']);
        }

        return $this->optimizationId;
    }

    public function userId(): UserId
    {
        if ($this->userId === null) {
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }
}
