<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Event;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\EventSourcing\AggregateChanged;

/** @noinspection LongInheritanceChainInspection */
class EditingBoundariesWasFinished extends AggregateChanged
{

    /** @var  ModflowId */
    private $modflowModelId;

    /** @var  UserId */
    private $userId;

    public static function byUser(UserId $userId, ModflowId $modflowModelId): EditingBoundariesWasFinished
    {
        $event = self::occur($modflowModelId->toString(), ['user_id' => $userId->toString()]);
        $event->modflowModelId = $modflowModelId;
        $event->userId = $userId;

        return $event;
    }

    public function modflowModelId(): ModflowId
    {
        if ($this->modflowModelId === null){
            $this->modflowModelId = ModflowId::fromString($this->aggregateId());
        }

        return $this->modflowModelId;
    }

    public function userId(): UserId
    {
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }
}
