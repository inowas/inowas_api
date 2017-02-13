<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Modflow\Model\ModflowBoundary;
use Inowas\Modflow\Model\ModflowModelId;
use Prooph\EventSourcing\AggregateChanged;

class ModflowModelBoundaryWasAdded extends AggregateChanged
{

    /** @var  ModflowModelId */
    private $modflowModelId;

    /** @var ModflowBoundary */
    private $boundary;

    public static function withBoundary(
        ModflowModelId $modflowModelId,
        ModflowBoundary $boundary
    ): ModflowModelBoundaryWasAdded
    {
        $event = self::occur(
            $modflowModelId->toString(), [
                'boundary' => serialize($boundary)
            ]
        );

        $event->modflowModelId = $modflowModelId;
        $event->boundary = $boundary;

        return $event;
    }

    public function modflowModelId(): ModflowModelId
    {
        if ($this->modflowModelId === null){
            $this->modflowModelId = ModflowModelId::fromString($this->aggregateId());
        }

        return $this->modflowModelId;
    }

    public function boundary(): ModflowBoundary
    {
        if ($this->boundary === null){
            $this->boundary = unserialize($this->payload['boundary']);
        }

        return $this->boundary;
    }
}
