<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model\Event;

use Inowas\Soilmodel\Model\GeologicalLayerId;
use Inowas\Soilmodel\Model\GeologicalLayerValues;
use Inowas\Soilmodel\Model\SoilmodelId;
use Prooph\EventSourcing\AggregateChanged;

class LayerValuesWereUpdated extends AggregateChanged
{

    /** @var  SoilmodelId */
    private $soilmodelId;

    /** @var  GeologicalLayerId $layerId */
    private $layerId;

    /** @var  GeologicalLayerValues $values */
    private $values;

    public static function forSoilmodelAndLayer(SoilmodelId $soilmodelId, GeologicalLayerId $layerId, GeologicalLayerValues $values): LayerValuesWereUpdated
    {
        $event = self::occur($soilmodelId->toString(),[
            'layer_id' => $layerId->toString(),
            'values' => $values->toArray()
        ]);

        $event->soilmodelId = $soilmodelId;
        $event->layerId = $layerId;
        $event->values = $values;

        return $event;
    }

    public function soilmodelId(): SoilmodelId
    {
        if ($this->soilmodelId === null){
            $this->soilmodelId = SoilmodelId::fromString($this->aggregateId());
        }

        return $this->soilmodelId;
    }

    public function layerId(): GeologicalLayerId {
        if ($this->layerId === null){
            $this->layerId = GeologicalLayerId::fromString($this->payload['layer_id']);
        }

        return $this->layerId;
    }

    public function values(): GeologicalLayerValues {
        if ($this->values === null){
            $this->values = GeologicalLayerValues::fromArray($this->payload['values']);
        }

        return $this->values;
    }
}
