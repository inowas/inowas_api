<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model\Event;

use Inowas\Common\Id\UserId;
use Inowas\Common\Soilmodel\GeologicalLayer;
use Inowas\Common\Soilmodel\SoilmodelId;
use Prooph\EventSourcing\AggregateChanged;

class SoilmodelGeologicalLayerWasAdded extends AggregateChanged
{

    /** @var  \Inowas\Common\Soilmodel\SoilmodelId */
    private $soilmodelId;

    /** @var  UserId */
    private $userId;

    /** @var  GeologicalLayer */
    private $layer;

    public static function byUserWithId(UserId $userId, SoilmodelId $soilmodelId, GeologicalLayer $geologicalLayer): SoilmodelGeologicalLayerWasAdded
    {
        $event = self::occur($soilmodelId->toString(),[
            'user_id' => $userId->toString(),
            'geological_layer' => $geologicalLayer->toArray()
        ]);

        $event->soilmodelId = $soilmodelId;
        $event->userId = $userId;
        $event->layer = $geologicalLayer;

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

    public function layer(): GeologicalLayer
    {
        if ($this->layer === null){
            $this->layer = GeologicalLayer::fromArray($this->payload['geological_layer']);
        }
        return $this->layer;
    }
}
