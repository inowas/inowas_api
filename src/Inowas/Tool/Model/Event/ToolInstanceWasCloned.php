<?php

declare(strict_types=1);

namespace Inowas\Tool\Model\Event;

use Inowas\Common\Id\UserId;
use Inowas\Tool\Model\ToolId;
use Inowas\Tool\Model\ToolType;
use Prooph\EventSourcing\AggregateChanged;

class ToolInstanceWasCloned extends AggregateChanged
{

    /** @var ToolId */
    private $id;

    /** @var ToolId */
    private $baseId;

    /** @var ToolType */
    private $type;

    /** @var UserId */
    private $userId;

    /**
     * @param ToolId $id
     * @param ToolId $baseId
     * @param UserId $userId
     * @param ToolType $type
     * @return ToolInstanceWasCloned
     * @internal param ToolId $id
     */
    public static function withParameters(
        ToolId $id,
        ToolId $baseId,
        UserId $userId,
        ToolType $type
    ): ToolInstanceWasCloned
    {
        /** @var ToolInstanceWasCloned $event */
        $event = self::occur($id->toString(),
            [
                'base_id' => $baseId->toString(),
                'user_id' => $userId->toString(),
                'type' => $type->toString()
            ]);

        $event->baseId = $baseId;
        $event->id = $id;
        $event->type = $type;
        return $event;
    }

    public function id(): ToolId
    {
        if ($this->id === null){
            $this->id = ToolId::fromString($this->aggregateId());
        }

        return $this->id;
    }

    public function baseId(): ToolId
    {
        if ($this->baseId === null){
            $this->baseId = ToolId::fromString($this->payload['base_id']);
        }

        return $this->baseId;
    }

    public function type(): ToolType
    {
        if ($this->type === null){
            $this->type = ToolType::fromString($this->payload['type']);
        }

        return $this->type;
    }

    public function userId(): UserId
    {
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }
}
