<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Modflow\Model\ModflowBoundary;
use Inowas\Modflow\Model\ModflowId;
use Inowas\Modflow\Model\UserId;
use Prooph\EventSourcing\AggregateChanged;

class BoundaryWasAdded extends AggregateChanged
{

    /** @var ModflowId */
    private $modflowId;

    /** @var ModflowBoundary */
    private $boundary;

    /** @var UserId */
    private $userId;

    public static function toBaseModel(
        UserId $userId,
        ModflowId $modflowId,
        ModflowBoundary $boundary
    ): BoundaryWasAdded
    {
        $event = self::occur(
            $modflowId->toString(), [
                'user_id' => $userId->toString(),
                'boundary' => serialize($boundary)
            ]
        );

        $event->modflowId = $modflowId;
        $event->boundary = $boundary;

        return $event;
    }

    public function modflowId(): ModflowId
    {
        if ($this->modflowId === null){
            $this->modflowId = ModflowId::fromString($this->modflowId());
        }

        return $this->modflowId;
    }

    public function boundary(): ModflowBoundary
    {
        if ($this->boundary === null){
            $this->boundary = unserialize($this->payload['boundary']);
        }

        return $this->boundary;
    }

    public function userId(): UserId
    {
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }
}
