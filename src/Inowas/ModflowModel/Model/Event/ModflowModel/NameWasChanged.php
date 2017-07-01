<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Event\ModflowModel;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\IdInterface;
use Inowas\Common\Modflow\ModelName;
use Inowas\Common\Id\UserId;
use Prooph\EventSourcing\AggregateChanged;

/** @noinspection LongInheritanceChainInspection */
class NameWasChanged extends AggregateChanged
{

    /** @var  IdInterface */
    private $modflowId;

    /** @var ModelName */
    private $name;

    /** @var UserId */
    private $userId;

    public static function byUserWithName(UserId $userId, IdInterface $modflowId, ModelName $name): NameWasChanged
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

    public function name(): ModelName
    {
        if ($this->name === null){
            $this->name = ModelName::fromString($this->payload['name']);
        }

        return $this->name;
    }
}
