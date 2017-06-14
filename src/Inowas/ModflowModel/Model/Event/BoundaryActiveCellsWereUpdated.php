<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Event;

use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\EventSourcing\AggregateChanged;

class BoundaryActiveCellsWereUpdated extends AggregateChanged
{

    /** @var ModflowId */
    private $modflowModelId;

    /** @var UserId */
    private $userId;

    /** @var BoundaryId */
    private $boundaryId;

    /** @var ActiveCells */
    private $activeCells;

    public static function of(ModflowId $modflowModelId, UserId $userId, BoundaryId $boundaryId, ActiveCells $activeCells): BoundaryActiveCellsWereUpdated
    {
        $event = self::occur(
            $modflowModelId->toString(), [
                'user_id' => $userId->toString(),
                'boundary_id' => $boundaryId->toString(),
                'active_cells' => $activeCells->toArray()
            ]
        );

        $event->modflowModelId = $modflowModelId;
        $event->boundaryId = $boundaryId;
        $event->activeCells = $activeCells;

        return $event;
    }

    public function modelId(): ModflowId
    {
        if ($this->modflowModelId === null){
            $this->modflowModelId = ModflowId::fromString($this->aggregateId());
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

    public function userId(): UserId
    {
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }

    public function activeCells(): ActiveCells
    {
        if ($this->activeCells === null){
            $this->activeCells = ActiveCells::fromArray($this->payload['active_cells']);
        }

        return $this->activeCells;
    }
}
