<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model\Event;

use Inowas\Common\Id\UserId;
use Inowas\Soilmodel\Model\BoreLogId;
use Inowas\Soilmodel\Model\SoilmodelId;
use Prooph\EventSourcing\AggregateChanged;

class SoilmodelBoreLogWasRemoved extends AggregateChanged
{

    /** @var  SoilmodelId */
    private $soilmodelId;

    /** @var  UserId */
    private $userId;

    /** @var  BoreLogId */
    private $boreLogId;

    public static function byUserWithId(UserId $userId, SoilmodelId $soilmodelId, BoreLogId $boreLogId): SoilmodelBoreLogWasRemoved
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
