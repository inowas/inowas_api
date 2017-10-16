<?php

declare(strict_types=1);

namespace Inowas\Tool\Model\Event;

use Inowas\Common\Id\UserId;
use Inowas\Common\Status\Visibility;
use Inowas\Tool\Model\ToolId;
use Prooph\EventSourcing\AggregateChanged;

class ToolInstanceVisibilityWasChanged extends AggregateChanged
{
    /** @var  ToolId */
    private $id;

    /** @var Visibility */
    private $visibility;

    /** @var  UserId */
    private $userId;

    public static function withVisibility(UserId $userId, ToolId $toolId, Visibility $visibility): ToolInstanceVisibilityWasChanged
    {
        /** @var ToolInstanceVisibilityWasChanged $event */
        $event = self::occur(
            $toolId->toString(), [
                'user_id' => $userId->toString(),
                'public' => $visibility->isPublic(),
            ]
        );

        $event->id = $toolId;
        $event->visibility = $visibility;
        $event->userId = $userId;

        return $event;
    }

    public function id(): ToolId
    {
        if ($this->id === null){
            $this->id = ToolId::fromString($this->aggregateId());
        }

        return $this->id;
    }

    public function userId(): UserId
    {
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }

    public function visibility(): Visibility
    {
        if ($this->visibility === null){
            $this->visibility = Visibility::fromBool($this->payload['public']);
        }

        return $this->visibility;
    }
}
