<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model\Event;

use Inowas\Modflow\Model\SoilmodelName;
use Inowas\Common\Id\UserId;
use Inowas\Soilmodel\Model\SoilmodelId;
use Prooph\EventSourcing\AggregateChanged;

class SoilmodelNameWasChanged extends AggregateChanged
{

    /** @var  SoilmodelId */
    private $soilmodelId;

    /** @var SoilmodelName */
    private $name;

    /** @var  UserId */
    private $userId;

    public static function byUserWithName(UserId $userId, SoilmodelId $soilmodelId, SoilmodelName $name): SoilmodelNameWasChanged
    {
        $event = self::occur(
            $soilmodelId->toString(), [
                'user_id' => $userId->toString(),
                'name' => $name->toString()
            ]
        );

        $event->userId = $userId;
        $event->soilmodelId = $soilmodelId;
        $event->name = $name;

        return $event;
    }

    public function soilmodelId(): SoilmodelId
    {
        if ($this->soilmodelId === null){
            $this->soilmodelId = SoilmodelId::fromString($this->aggregateId());
        }

        return $this->soilmodelId;
    }

    public function userId(): UserId
    {
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }

    public function name(): SoilmodelName
    {
        if ($this->name === null){
            $this->name = SoilmodelName::fromString($this->payload['name']);
        }

        return $this->name;
    }
}
