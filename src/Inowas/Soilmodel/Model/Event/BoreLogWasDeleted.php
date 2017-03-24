<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model\Event;

use Inowas\Common\Id\UserId;
use Inowas\Soilmodel\Model\BoreLogId;
use Prooph\EventSourcing\AggregateChanged;

class BoreLogWasDeleted extends AggregateChanged
{

    /** @var  BoreLogId */
    private $boreLogId;

    /** @var  UserId */
    private $userId;

    public static function byUserWithId(UserId $userId, BoreLogId $boreLogId): BoreLogWasDeleted
    {
        $event = self::occur($boreLogId->toString(),[
            'user_id' => $userId->toString()
        ]);

        $event->$boreLogId = $boreLogId;
        $event->userId = $userId;

        return $event;
    }

    public function boreLogId(): BoreLogId
    {
        if ($this->boreLogId === null){
            $this->boreLogId = BoreLogId::fromString($this->aggregateId());
        }

        return $this->boreLogId;
    }

    public function userId(): UserId{
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }
}
