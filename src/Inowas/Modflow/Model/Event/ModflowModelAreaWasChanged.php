<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Modflow\Model\ModflowModelArea;
use Inowas\Modflow\Model\ModflowModelId;
use Prooph\EventSourcing\AggregateChanged;

class ModflowModelAreaWasChanged extends AggregateChanged
{

    /** @var  ModflowModelId */
    private $modflowModelId;

    /** @var ModflowModelArea */
    private $area;

    public static function withArea(ModflowModelId $modflowModelId, ModflowModelArea $area): ModflowModelAreaWasChanged
    {
        $event = self::occur(
            $modflowModelId->toString(), [
                'area' => serialize($area)
            ]
        );

        $event->modflowModelId = $modflowModelId;
        $event->area = $area;

        return $event;
    }

    public function modflowModelId(): ModflowModelId
    {
        if ($this->modflowModelId === null){
            $this->modflowModelId = ModflowModelId::fromString($this->aggregateId());
        }

        return $this->modflowModelId;
    }

    public function area(): ModflowModelArea
    {
        if ($this->area === null){
            $this->area = unserialize($this->payload['area']);
        }

        return $this->area;
    }
}
