<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Event;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\Optimization;
use Prooph\EventSourcing\AggregateChanged;

/** @noinspection LongInheritanceChainInspection */

class OptimizationWasUpdated extends AggregateChanged
{

    /** @var ModflowId */
    private $modflowId;

    /** @var  UserId */
    private $userId;

    /** @var Optimization */
    private $optimization;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param UserId $userId
     * @param ModflowId $modflowId
     * @param Optimization $optimization
     * @return self
     */
    public static function byUserToModel(UserId $userId, ModflowId $modflowId, Optimization $optimization): self
    {
        /** @var self $event */
        $event = self::occur(
            $modflowId->toString(), [
                'user_id' => $userId->toString(),
                'optimization' => $optimization->toArray()
            ]
        );

        $event->modflowId = $modflowId;
        $event->userId = $userId;
        $event->optimization = $optimization;

        return $event;
    }

    public function modelId(): ModflowId
    {
        if ($this->modflowId === null) {
            $this->modflowId = ModflowId::fromString($this->aggregateId());
        }

        return $this->modflowId;
    }

    public function optimization(): Optimization
    {
        if ($this->optimization === null) {
            $this->optimization = Optimization::fromArray($this->payload['optimization']);
        }

        return $this->optimization;
    }

    public function userId(): UserId
    {
        if ($this->userId === null) {
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }
}
