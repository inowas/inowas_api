<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\SoilModelId;
use Prooph\EventSourcing\AggregateChanged;

class ModflowModelSoilModelIdWasChanged extends AggregateChanged
{

    /** @var  \Inowas\Common\Id\ModflowId */
    private $modflowModelId;

    /** @var \Inowas\Common\Id\SoilModelId */
    private $soilmodelId;

    public static function withSoilmodelId(ModflowId $modflowModelId, SoilModelId $soilModelId): ModflowModelSoilModelIdWasChanged
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

    public function soilModelId(): SoilModelId
    {
        if ($this->soilmodelId === null){
            $this->soilmodelId = SoilModelId::fromString($this->payload['soilmodel_id']);
        }

        return $this->soilmodelId;
    }
}
