<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model\Event;

use Inowas\Common\Id\UserId;
use Inowas\ModflowModel\Model\BoreLogName;
use Inowas\Common\Soilmodel\BoreLogId;
use Prooph\EventSourcing\AggregateChanged;

class BoreLogNameWasChanged extends AggregateChanged
{

    /** @var  BoreLogId */
    private $boreLogId;

    /** @var  UserId */
    private $userId;

    /** @var  BoreLogName */
    private $name;

    public static function byUserWithName(UserId $userId, BoreLogId $boreLogId, BoreLogName $name): BoreLogNameWasChanged
    {
        $event = self::occur($boreLogId->toString(),[
            'user_id' => $userId->toString(),
            'name' => $name->toString()
        ]);

        $event->boreLogId = $boreLogId;
        $event->name = $name;
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

    public function userId(): UserId
    {
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
}
