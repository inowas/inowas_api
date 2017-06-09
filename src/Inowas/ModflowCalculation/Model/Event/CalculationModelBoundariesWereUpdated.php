<?php

declare(strict_types=1);

namespace Inowas\ModflowCalculation\Model\Event;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\EventSourcing\AggregateChanged;

/** @noinspection LongInheritanceChainInspection */
class CalculationModelBoundariesWereUpdated extends AggregateChanged
{
    /** @var  ModflowId */
    private $calculationId;

    /** @var  UserId */
    private $userId;

    public static function withProps(
        UserId $userId,
        ModflowId $calculationId
    ): CalculationModelBoundariesWereUpdated
    {
        $event = self::occur($calculationId->toString(),[
            'user_id' => $userId->toString()
        ]);

        $event->userId = $userId;
        $event->calculationId = $calculationId;
        return $event;
    }

    public function calculationId(): ModflowId
    {
        if ($this->calculationId === null){
            $this->calculationId = ModflowId::fromString($this->aggregateId());
        }

        return $this->calculationId;
    }

    public function userId(): UserId
    {
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }
}
