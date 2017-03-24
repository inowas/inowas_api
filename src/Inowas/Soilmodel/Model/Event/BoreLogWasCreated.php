<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model\Event;

use Inowas\Common\Id\UserId;
use Inowas\Soilmodel\Model\BoreLogId;
use Inowas\Soilmodel\Model\BoreLogLocation;
use Inowas\Soilmodel\Model\BoreLogName;
use Prooph\EventSourcing\AggregateChanged;

class BoreLogWasCreated extends AggregateChanged
{

    /** @var  BoreLogId */
    private $boreLogId;

    /** @var  UserId */
    private $userId;

    /** @var  BoreLogName */
    private $name;

    /** @var  BoreLogLocation */
    private $location;

    public static function byUserWithId(UserId $userId, BoreLogId $boreLogId, BoreLogName $name, BoreLogLocation $location): BoreLogWasCreated
    {
        $event = self::occur($boreLogId->toString(),[
            'user_id' => $userId->toString(),
            'name' => $name->toString(),
            'location' => $location->toArray()
        ]);

        $event->boreLogId = $boreLogId;
        $event->userId = $userId;
        $event->name = $name;
        $event->location;

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

    public function name(): BoreLogName
    {
        if ($this->name === null){
            $this->name = BoreLogName::fromString($this->payload['name']);
        }

        return $this->name;
    }

    public function location(): BoreLogLocation
    {
        if ($this->location === null){
            $this->location = BoreLogLocation::fromArray($this->payload['location']);
        }

        return $this->location;
    }
}
