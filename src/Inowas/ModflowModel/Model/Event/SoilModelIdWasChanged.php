<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Event;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Soilmodel\SoilmodelId;
use Prooph\EventSourcing\AggregateChanged;

class SoilModelIdWasChanged extends AggregateChanged
{

    /** @var ModflowId */
    private $modflowModelId;

    private $soilmodelId;

    public static function withSoilmodelId(ModflowId $modflowModelId, SoilmodelId $soilModelId): SoilModelIdWasChanged
    {
        $event = self::occur(
            $modflowModelId->toString(), [
                'soilmodel_id' => $soilModelId->toString()
            ]
        );

        $event->modflowModelId = $modflowModelId;
        $event->soilmodelId = $soilModelId;

        return $event;
    }

    public function modflowModelId(): ModflowId
    {
        if ($this->modflowModelId === null){
            $this->modflowModelId = ModflowId::fromString($this->aggregateId());
        }

        return $this->modflowModelId;
    }

    public function soilModelId(): SoilmodelId
    {
        if ($this->soilmodelId === null){
            $this->soilmodelId = SoilmodelId::fromString($this->payload['soilmodel_id']);
        }

        return $this->soilmodelId;
    }
}
