<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Event;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\IdInterface;
use Inowas\Common\Modflow\Modelname;
use Inowas\Common\Id\UserId;
use Prooph\EventSourcing\AggregateChanged;

class NameWasChanged extends AggregateChanged
{

    /** @var  IdInterface */
    private $modflowId;

    /** @var Modelname */
    private $name;

    /** @var UserId */
    private $userId;

    public static function byUserWithName(UserId $userId, IdInterface $modflowId, Modelname $name): NameWasChanged
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

    public function name(): Modelname
    {
        if ($this->name === null){
            $this->name = Modelname::fromString($this->payload['name']);
        }

        return $this->name;
    }
}
