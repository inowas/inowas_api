<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model\Event;

use Inowas\Common\Soilmodel\GeologicalLayerId;
use Inowas\Common\Soilmodel\GeologicalLayerNumber;
use Inowas\Common\Soilmodel\GeologicalLayerValues;
use Inowas\Common\Soilmodel\SoilmodelId;
use Prooph\EventSourcing\AggregateChanged;

class LayerValuesWereUpdated extends AggregateChanged
{

    /** @var  SoilmodelId */
    private $soilmodelId;

    /** @var  \Inowas\Common\Soilmodel\GeologicalLayerId $layerId */
    private $layerId;

    /** @var  GeologicalLayerNumber $layerNumber */
    private $layerNumber;

    /** @var  \Inowas\Common\Soilmodel\GeologicalLayerValues $values */
    private $values;

    public static function forSoilmodelAndLayer(SoilmodelId $soilmodelId, GeologicalLayerId $layerId, GeologicalLayerNumber $layerNumber, GeologicalLayerValues $values): LayerValuesWereUpdated
    {
        $event = self::occur($soilmodelId->toString(),[
            'layer_id' => $layerId->toString(),
            'layer_number' => $layerNumber->toInteger(),
            'values' => $values->toArray()
        ]);

        $event->soilmodelId = $soilmodelId;
        $event->layerId = $layerId;
        $event->layerNumber = $layerNumber;
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

    public function layerId(): GeologicalLayerId
    {
        if ($this->layerId === null){
            $this->layerId = GeologicalLayerId::fromString($this->payload['layer_id']);
        }

        return $this->layerId;
    }

    public function layerNumber(): GeologicalLayerNumber
    {
        if ($this->layerNumber === null){
            $this->layerNumber = GeologicalLayerNumber::fromInteger($this->payload['layer_number']);
        }

        return $this->layerNumber;
    }

    public function values(): GeologicalLayerValues
    {
        if ($this->values === null){
            $this->values = GeologicalLayerValues::fromArray($this->payload['values']);
        }

        return $this->values;
    }
}
