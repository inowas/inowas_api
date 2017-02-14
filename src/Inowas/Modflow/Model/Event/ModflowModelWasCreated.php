<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Modflow\Model\ModflowId;
use Inowas\Modflow\Model\ModflowModelId;
use Inowas\Modflow\Model\UserId;
use Prooph\EventSourcing\AggregateChanged;

class ModflowModelWasCreated extends AggregateChanged
{

    /** @var  ModflowId */
    private $modflowModelId;

    /** @var  UserId */
    private $userId;

    public static function byUserWithModflowId(UserId $userId, ModflowId $modflowModelId): ModflowModelWasCreated
    {
        $event = self::occur($modflowModelId->toString(),[
            'user_id' => $userId->toString()
        ]);

        $event->modflowModelId = $modflowModelId;
        $event->userId = $userId;

        return $event;
    }

    public function modflowModelId(): ModflowId
    {
        if ($this->modflowModelId === null){
            $this->modflowModelId = ModflowModelId::fromString($this->aggregateId());
        }

        return $this->modflowModelId;
    }

    public function userId(): UserId{
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }
}
