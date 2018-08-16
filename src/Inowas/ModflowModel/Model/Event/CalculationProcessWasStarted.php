<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Event;

use Inowas\Common\Calculation\CalculationState;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\EventSourcing\AggregateChanged;

/** @noinspection LongInheritanceChainInspection */

class CalculationProcessWasStarted extends AggregateChanged
{
    /** @var  ModflowId */
    private $modelId;

    /** @var  UserId */
    private $userId;

    public static function withId(UserId $userId, ModflowId $modflowId): self
    {
        /** @var self $event */
        $event = self::occur(
            $modflowId->toString(), [
                'user_id' => $userId->toString()
            ]
        );

        return $event;
    }

    public function modelId(): ModflowId
    {
        if ($this->modelId === null) {
            $this->modelId = ModflowId::fromString($this->aggregateId());
        }

        return $this->modelId;
    }

    public function state(): CalculationState
    {
        return CalculationState::calculationProcessStarted();
    }

    public function userId(): UserId
    {
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }
}
