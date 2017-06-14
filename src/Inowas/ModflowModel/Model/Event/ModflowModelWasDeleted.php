<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Event;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\EventSourcing\AggregateChanged;

/** @noinspection LongInheritanceChainInspection */
class ModflowModelWasDeleted extends AggregateChanged
{
    /** @var ModflowId */
    private $modelId;

    /** @var UserId */
    private $userId;

    /** @var  ModflowId */
    protected $calculationId;

    public static function byUserWitModelId(
        ModflowId $modflowId,
        UserId $userId
    ): ModflowModelWasDeleted
    {
        $event = self::occur($modflowId->toString(),[
            'user_id' => $userId->toString()
        ]);

        $event->modelId = $modflowId;
        $event->userId = $userId;

        return $event;
    }

    public function modelId(): ModflowId
    {
        if ($this->modelId === null){
            $this->modelId = ModflowId::fromString($this->aggregateId());
        }

        return $this->modelId;
    }

    public function userId(): UserId
    {
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }
}
