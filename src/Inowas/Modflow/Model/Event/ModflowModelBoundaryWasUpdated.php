<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Common\Boundaries\ModflowBoundary;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\EventSourcing\AggregateChanged;

class ModflowModelBoundaryWasUpdated extends AggregateChanged
{
    /** @var \Inowas\Common\Id\ModflowId */
    private $modflowId;

    /** @var ModflowBoundary */
    private $boundary;

    /** @var \Inowas\Common\Id\UserId */
    private $userId;

    public static function ofBaseModel(
        UserId $userId,
        ModflowId $modflowId,
        ModflowBoundary $boundary
    ): ModflowModelBoundaryWasUpdated
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
            $this->modflowId = ModflowId::fromString($this->aggregateId());
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
