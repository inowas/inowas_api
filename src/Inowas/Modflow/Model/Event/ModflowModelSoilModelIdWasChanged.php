<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Modflow\Model\ModflowModelId;
use Inowas\Modflow\Model\SoilModelId;
use Prooph\EventSourcing\AggregateChanged;

class ModflowModelSoilModelIdWasChanged extends AggregateChanged
{

    /** @var  ModflowModelId */
    private $modflowModelId;

    /** @var SoilModelId */
    private $soilmodelId;

    public static function withSoilmodelId(ModflowModelId $modflowModelId, SoilModelId $soilModelId): ModflowModelSoilModelIdWasChanged
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

    public function modflowModelId(): ModflowModelId
    {
        if ($this->modflowModelId === null){
            $this->modflowModelId = ModflowModelId::fromString($this->aggregateId());
        }

        return $this->modflowModelId;
    }

    public function soilModelId(): SoilModelId
    {
        if ($this->soilmodelId === null){
            $this->soilmodelId = SoilModelId::fromString($this->payload['soilmodel_id']);
        }

        return $this->soilmodelId;
    }
}
