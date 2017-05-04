<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model\Event;

use Inowas\Common\Id\UserId;
use Inowas\Common\Soilmodel\SoilmodelDescription;
use Inowas\Common\Soilmodel\SoilmodelId;
use Prooph\EventSourcing\AggregateChanged;

class SoilmodelDescriptionWasChanged extends AggregateChanged
{

    /** @var  \Inowas\Common\Soilmodel\SoilmodelId */
    private $soilmodelId;

    /** @var SoilmodelDescription */
    private $description;

    /** @var  UserId */
    private $userId;

    public static function byUserWithName(UserId $userId, SoilmodelId $soilmodelId, SoilmodelDescription $description): SoilmodelDescriptionWasChanged
    {
        $event = self::occur(
            $soilmodelId->toString(), [
                'user_id' => $userId->toString(),
                'description' => $description->toString()
            ]
        );

        $event->userId = $userId;
        $event->soilmodelId = $soilmodelId;
        $event->description = $description;

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

    public function description(): SoilmodelDescription
    {
        if ($this->description === null){
            $this->description = SoilmodelDescription::fromString($this->payload['description']);
        }

        return $this->description;
    }
}
