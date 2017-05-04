<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model\Event;

use Inowas\Common\Id\UserId;
use Inowas\Common\Soilmodel\GeologicalLayerId;
use Inowas\Common\Soilmodel\SoilmodelId;
use Prooph\EventSourcing\AggregateChanged;

class SoilmodelGeologicalLayerWasRemoved extends AggregateChanged
{

    /** @var  SoilmodelId */
    private $soilmodelId;

    /** @var  UserId */
    private $userId;

    /** @var GeologicalLayerId */
    private $layerId;

    public static function byUserWithId(UserId $userId, SoilmodelId $soilmodelId, GeologicalLayerId $geologicalLayerId): SoilmodelGeologicalLayerWasRemoved
    {
        $event = self::occur($soilmodelId->toString(),[
            'user_id' => $userId->toString(),
            'geological_layer_id' => $geologicalLayerId->toString()
        ]);

        $event->soilmodelId = $soilmodelId;
        $event->userId = $userId;
        $event->layerId = $geologicalLayerId;

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

    public function layerId(): GeologicalLayerId
    {
        if ($this->layerId === null){
            $this->layerId = GeologicalLayerId::fromString($this->payload['geological_layer_id']);
        }
        return $this->layerId;
    }
}
