<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model\Event;

use Inowas\Common\Id\UserId;
use Inowas\ModflowModel\Model\BoreLogLocation;
use Inowas\ModflowModel\Model\BoreLogName;
use Inowas\Common\Soilmodel\BoreLogId;
use Prooph\EventSourcing\AggregateChanged;

class BoreLogLocationWasChanged extends AggregateChanged
{

    /** @var  BoreLogId */
    private $boreLogId;

    /** @var  UserId */
    private $userId;

    /** @var  BoreLogLocation */
    private $location;

    public static function byUserWithLocation(UserId $userId, BoreLogId $boreLogId, BoreLogLocation $location): BoreLogLocationWasChanged
    {
        $event = self::occur($boreLogId->toString(),[
            'user_id' => $userId->toString(),
            'location' => $location->toArray()
        ]);

        $event->boreLogId = $boreLogId;
        $event->userId = $userId;
        $event->location = $location;

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

    public function location(): BoreLogLocation
    {
        if ($this->location === null){
            $this->location = BoreLogName::fromString($this->payload['location']);
        }

        return $this->location;
    }
}
