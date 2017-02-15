<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Modflow\Model\ModflowIdInterface;
use Inowas\Modflow\Model\ModflowId;
use Inowas\Modflow\Model\UserId;
use Prooph\EventSourcing\AggregateChanged;

class ModflowModelWasCreated extends AggregateChanged
{

    /** @var  ModflowIdInterface */
    private $modflowModelId;

    /** @var  UserId */
    private $userId;

    public static function byUserWithModflowId(UserId $userId, ModflowIdInterface $modflowModelId): ModflowModelWasCreated
    {
        $event = self::occur($modflowModelId->toString(),[
            'user_id' => $userId->toString()
        ]);

        $event->modflowModelId = $modflowModelId;
        $event->userId = $userId;

        return $event;
    }

    public function modflowModelId(): ModflowIdInterface
    {
        if ($this->modflowModelId === null){
            $this->modflowModelId = ModflowId::fromString($this->modflowId());
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
