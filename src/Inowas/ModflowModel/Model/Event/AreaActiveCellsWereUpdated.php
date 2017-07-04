<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Event;

use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\EventSourcing\AggregateChanged;

/** @noinspection LongInheritanceChainInspection */
class AreaActiveCellsWereUpdated extends AggregateChanged
{

    /** @var ModflowId */
    private $modflowId;

    /** @var  UserId */
    private $userId;

    /** @var  ActiveCells */
    private $activeCells;

    public static function byUserAndModel(
        UserId $userId,
        ModflowId $modflowId,
        ActiveCells $activeCells
    ): AreaActiveCellsWereUpdated
    {
        $event = self::occur(
            $modflowId->toString(), [
                'user_id' => $userId->toString(),
                'active_cells' => $activeCells->toArray()
            ]
        );

        $event->modflowId = $modflowId;
        $event->userId = $userId;
        $event->activeCells = $activeCells;

        return $event;
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

    public function activeCells(): ActiveCells
    {
        if ($this->activeCells === null){
            $this->activeCells = ActiveCells::fromArray($this->payload['active_cells']);
        }

        return $this->activeCells;
    }
}
