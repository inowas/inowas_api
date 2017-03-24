<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model\Event;

use Inowas\Common\Id\UserId;
use Inowas\Soilmodel\Model\BoreLogId;
use Inowas\Soilmodel\Model\HorizonId;
use Prooph\EventSourcing\AggregateChanged;

class BoreLogHorizonWasRemoved extends AggregateChanged
{

    /** @var  BoreLogId */
    private $boreLogId;

    /** @var  UserId */
    private $userId;

    /** @var  HorizonId */
    private $horizonId;

    public static function byUserWithHorizonId(UserId $userId, BoreLogId $boreLogId, HorizonId $horizonId): BoreLogHorizonWasRemoved
    {
        $event = self::occur($boreLogId->toString(),[
            'user_id' => $userId->toString(),
            'horizon_id' => $horizonId->toString()
        ]);

        $event->boreLogId = $boreLogId;
        $event->userId = $userId;
        $event->horizonId = $horizonId;

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

    public function horizonId(): HorizonId
    {
        if ($this->horizonId === null){
            $this->horizonId = HorizonId::fromString($this->payload['horizon_id']);
        }

        return $this->horizonId;
    }
}
