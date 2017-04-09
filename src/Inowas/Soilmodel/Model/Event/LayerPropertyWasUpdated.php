<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model\Event;

use Inowas\Common\Id\UserId;
use Inowas\Common\Soilmodel\AbstractSoilproperty;
use Inowas\Common\Soilmodel\BottomElevation;
use Inowas\Common\Soilmodel\HydraulicAnisotropy;
use Inowas\Common\Soilmodel\HydraulicConductivityX;
use Inowas\Common\Soilmodel\HydraulicConductivityY;
use Inowas\Common\Soilmodel\HydraulicConductivityZ;
use Inowas\Common\Soilmodel\SpecificStorage;
use Inowas\Common\Soilmodel\SpecificYield;
use Inowas\Common\Soilmodel\TopElevation;
use Inowas\Common\Soilmodel\VerticalHydraulicConductivity;
use Inowas\Soilmodel\Model\GeologicalLayerId;
use Inowas\Soilmodel\Model\SoilmodelId;
use Prooph\EventSourcing\AggregateChanged;

class LayerPropertyWasUpdated extends AggregateChanged
{

    /** @var  GeologicalLayerId */
    private $layerId;

    /** @var  SoilmodelId */
    private $soilmodelId;

    /** @var  UserId */
    private $userId;

    /** @var  AbstractSoilproperty */
    private $property;

    /** @var  string */
    private $type;


    public static function forSoilmodelAndLayer(UserId $userId, SoilmodelId $soilmodelId, GeologicalLayerId $layerId, AbstractSoilproperty $property): LayerPropertyWasUpdated
    {
        $event = self::occur($soilmodelId->toString(),[
            'user_id' => $userId->toString(),
            'layer_id' => $layerId->toString(),
            'type' => $property->identifier(),
            'property' => $property->toValue()
        ]);

        $event->layerId = $layerId;
        $event->soilmodelId = $soilmodelId;
        $event->userId = $userId;
        $event->property = $property;
        $event->type = $property->identifier();

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

    public function property(): AbstractSoilproperty
    {
        if ($this->property === null){
            $type = $this->payload['type'];

            if ($type == BottomElevation::TYPE){
                $this->property = BottomElevation::fromLayerValue($this->payload['property']);
            }

            if ($type == HydraulicAnisotropy::TYPE){
                $this->property = HydraulicAnisotropy::fromLayerValue($this->payload['property']);
            }

            if ($type == HydraulicConductivityX::TYPE){
                $this->property = HydraulicConductivityX::fromLayerValue($this->payload['property']);
            }

            if ($type == HydraulicConductivityY::TYPE){
                $this->property = HydraulicConductivityY::fromLayerValue($this->payload['property']);
            }

            if ($type == HydraulicConductivityZ::TYPE){
                $this->property = HydraulicConductivityZ::fromLayerValue($this->payload['property']);
            }

            if ($type == SpecificStorage::TYPE){
                $this->property = SpecificStorage::fromLayerValue($this->payload['property']);
            }

            if ($type == SpecificYield::TYPE){
                $this->property = SpecificYield::fromLayerValue($this->payload['property']);
            }

            if ($type == TopElevation::TYPE){
                $this->property = TopElevation::fromLayerValue($this->payload['property']);
            }

            if ($type == VerticalHydraulicConductivity::TYPE){
                $this->property = VerticalHydraulicConductivity::fromLayerValue($this->payload['property']);
            }
        }

        return $this->property;
    }
}
