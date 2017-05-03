<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model\Event;

use Inowas\Common\Id\UserId;
use Inowas\Common\Soilmodel\BoreLogId;
use Inowas\Common\Soilmodel\SoilmodelId;
use Prooph\EventSourcing\AggregateChanged;

class SoilmodelBoreLogWasAdded extends AggregateChanged
{

    /** @var  \Inowas\Common\Soilmodel\SoilmodelId */
    private $soilmodelId;

    /** @var  UserId */
    private $userId;

    /** @var  BoreLogId */
    private $boreLogId;

    public static function byUserWithId(UserId $userId, SoilmodelId $soilmodelId, BoreLogId $boreLogId): SoilmodelBoreLogWasAdded
    {
        $event = self::occur($soilmodelId->toString(),[
            'user_id' => $userId->toString(),
            'borelog_id' => $boreLogId->toString()
        ]);

        $event->soilmodelId = $soilmodelId;
        $event->userId = $userId;
        $event->boreLogId = $boreLogId;

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

    public function boreLogId(): BoreLogId
    {
        if ($this->boreLogId === null){
            $this->boreLogId = BoreLogId::fromString($this->payload['borelog_id']);
        }

        return $this->boreLogId;
    }
}
