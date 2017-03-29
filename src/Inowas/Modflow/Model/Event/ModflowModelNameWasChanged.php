<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\IdInterface;
use Inowas\Modflow\Model\ModflowModelName;
use Inowas\Common\Id\UserId;
use Prooph\EventSourcing\AggregateChanged;

class ModflowModelNameWasChanged extends AggregateChanged
{

    /** @var  IdInterface */
    private $modflowId;

    /** @var ModflowModelName */
    private $name;

    /** @var  \Inowas\Common\Id\UserId */
    private $userId;

    public static function byUserWithName(UserId $userId, IdInterface $modflowId, ModflowModelName $name): ModflowModelNameWasChanged
    {
        $event = self::occur(
            $modflowId->toString(), [
                'user_id' => $userId->toString(),
                'name' => $name->toString()
            ]
        );

        $event->userId = $userId;
        $event->modflowId = $modflowId;
        $event->name = $name;

        return $event;
    }

    public function modflowId(): IdInterface
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

    public function name(): ModflowModelName
    {
        if ($this->name === null){
            $this->name = ModflowModelName::fromString($this->payload['name']);
        }

        return $this->name;
    }
}
