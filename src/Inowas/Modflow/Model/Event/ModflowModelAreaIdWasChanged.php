<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Modflow\Model\BoundaryId;
use Inowas\Modflow\Model\ModflowModelId;
use Prooph\EventSourcing\AggregateChanged;

class ModflowModelAreaIdWasChanged extends AggregateChanged
{

    /** @var  ModflowModelId */
    private $modflowModelId;

    /** @var BoundaryId */
    private $areaId;

    public static function withAreaId(ModflowModelId $modflowModelId, BoundaryId $areaId): ModflowModelAreaIdWasChanged
    {
        $event = self::occur(
            $modflowModelId->toString(), [
                'area_id' => $areaId->toString()
            ]
        );

        $event->modflowModelId = $modflowModelId;
        $event->areaId = $areaId;

        return $event;
    }

    public function modflowModelId(): ModflowModelId
    {
        if ($this->modflowModelId === null){
            $this->modflowModelId = ModflowModelId::fromString($this->aggregateId());
        }

        return $this->modflowModelId;
    }

    public function areaId(): BoundaryId
    {
        if ($this->areaId === null){
            $this->areaId = BoundaryId::fromString($this->payload['area_id']);
        }

        return $this->areaId;
    }
}
