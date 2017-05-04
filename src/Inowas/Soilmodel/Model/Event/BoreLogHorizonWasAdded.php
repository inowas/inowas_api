<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model\Event;

use Inowas\Common\Id\UserId;
use Inowas\Common\Soilmodel\BoreLogId;
use Inowas\Common\Soilmodel\Horizon;
use Prooph\EventSourcing\AggregateChanged;

class BoreLogHorizonWasAdded extends AggregateChanged
{

    /** @var  \Inowas\Common\Soilmodel\BoreLogId */
    private $boreLogId;

    /** @var  UserId */
    private $userId;

    /** @var  \Inowas\Common\Soilmodel\Horizon */
    private $horizon;

    public static function byUserWithHorizon(UserId $userId, BoreLogId $boreLogId, Horizon $horizon): BoreLogHorizonWasAdded
    {
        $event = self::occur($boreLogId->toString(),[
            'user_id' => $userId->toString(),
            'horizon' => $horizon->toArray()
        ]);

        $event->boreLogId = $boreLogId;
        $event->userId = $userId;
        $event->horizon = $horizon;

        return $event;
    }

    public function boreLogId(): BoreLogId
    {
        if ($this->boreLogId === null){
            $this->boreLogId = BoreLogId::fromString($this->aggregateId());
        }

        return $this->boreLogId;
    }

    public function userId(): UserId
    {
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }

    public function horizon(): Horizon
    {
        if ($this->horizon === null){
            $this->horizon = Horizon::fromArray($this->payload['horizon']);
        }

        return $this->horizon;
    }
}
