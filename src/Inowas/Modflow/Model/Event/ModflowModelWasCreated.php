<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Common\Id\IdInterface;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\EventSourcing\AggregateChanged;

class ModflowModelWasCreated extends AggregateChanged
{

    /** @var  \Inowas\Common\Id\IdInterface */
    private $modflowModelId;

    /** @var  UserId */
    private $userId;

    public static function byUserWithModflowId(UserId $userId, IdInterface $modflowModelId): ModflowModelWasCreated
    {
        $event = self::occur($modflowModelId->toString(),[
            'user_id' => $userId->toString()
        ]);

        $event->modflowModelId = $modflowModelId;
        $event->userId = $userId;

        return $event;
    }

    public function modflowModelId(): IdInterface
    {
        if ($this->modflowModelId === null){
            $this->modflowModelId = ModflowId::fromString($this->aggregateId());
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
