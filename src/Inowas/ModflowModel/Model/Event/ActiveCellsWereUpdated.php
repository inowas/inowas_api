<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Event;

use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\EventSourcing\AggregateChanged;

class ActiveCellsWereUpdated extends AggregateChanged
{

    /** @var ModflowId */
    private $modflowId;

    /** @var  BoundaryId */
    private $boundaryId;

    /** @var  UserId */
    private $userId;

    /** @var  ActiveCells */
    private $activeCells;

    /** @var  string */
    private $type;

    public static function toBaseModel(
        UserId $userId,
        ModflowId $modflowId,
        BoundaryId $boundaryId,
        string $type,
        ActiveCells $activeCells
    ): ActiveCellsWereUpdated
    {
        $event = self::occur(
            $modflowId->toString(), [
                'user_id' => $userId->toString(),
                'boundary_id' => $boundaryId->toString(),
                'active_cells' => $activeCells->toArray(),
                'boundary_type' => $type
            ]
        );

        $event->modflowId = $modflowId;
        $event->boundaryId = $boundaryId;
        $event->userId = $userId;
        $event->activeCells = $activeCells;

        return $event;
    }

    public function activeCells(): ActiveCells
    {
        if ($this->activeCells === null){
            $this->activeCells = ActiveCells::fromArray($this->payload['active_cells']);
        }

        return $this->activeCells;
    }

    public function boundaryId(): BoundaryId
    {
        if ($this->boundaryId === null){
            $this->boundaryId = BoundaryId::fromString($this->payload['boundary_id']);
        }

        return $this->boundaryId;
    }

    public function modflowId(): ModflowId
    {
        if ($this->modflowId === null){
            $this->modflowId = ModflowId::fromString($this->aggregateId());
        }

        return $this->modflowId;
    }

    public function userId(): UserId
    {
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }

    public function type(): string
    {
        if ($this->type === null){
            $this->type = $this->payload['boundary_type'];
        }

        return $this->type;
    }
}
