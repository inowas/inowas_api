<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Modflow\Model\BoundaryId;
use Inowas\Modflow\Model\ModflowModelId;
use Prooph\EventSourcing\AggregateChanged;

class ModflowModelBoundaryWasAdded extends AggregateChanged
{

    /** @var  ModflowModelId */
    private $modflowModelId;

    /** @var BoundaryId */
    private $boundaryId;

    public static function withBoundaryId(ModflowModelId $modflowModelId, BoundaryId $boundaryId): ModflowModelBoundaryWasAdded
    {
        $event = self::occur(
            $modflowModelId->toString(), [
                'boundary_id' => $boundaryId->toString()
            ]
        );

        $event->modflowModelId = $modflowModelId;
        $event->boundaryId = $boundaryId;

        return $event;
    }

    public function modflowModelId(): ModflowModelId
    {
        if ($this->modflowModelId === null){
            $this->modflowModelId = ModflowModelId::fromString($this->aggregateId());
        }

        return $this->modflowModelId;
    }

    public function boundaryId(): BoundaryId
    {
        if ($this->boundaryId === null){
            $this->boundaryId = BoundaryId::fromString($this->payload['boundary_id']);
        }

        return $this->boundaryId;
    }
}
