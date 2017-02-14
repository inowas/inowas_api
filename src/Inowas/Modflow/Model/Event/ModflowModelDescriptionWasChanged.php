<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Modflow\Model\ModflowModelDescription;
use Inowas\Modflow\Model\ModflowModelId;
use Inowas\Modflow\Model\UserId;
use Prooph\EventSourcing\AggregateChanged;

class ModflowModelDescriptionWasChanged extends AggregateChanged
{

    /** @var  ModflowModelId */
    private $modflowModelId;

    /** @var ModflowModelDescription */
    private $description;

    /** @var  UserId */
    private $userId;

    public static function withDescription(UserId $userId, ModflowModelId $modflowModelId, ModflowModelDescription $description): ModflowModelDescriptionWasChanged
    {
        $event = self::occur(
            $modflowModelId->toString(), [
                'user_id' => $userId->toString(),
                'description' => $description->toString()
            ]
        );

        $event->modflowModelId = $modflowModelId;
        $event->description = $description;
        $event->userId = $userId;

        return $event;
    }

    public function modflowModelId(): ModflowModelId
    {
        if ($this->modflowModelId === null){
            $this->modflowModelId = ModflowModelId::fromString($this->aggregateId());
        }

        return $this->modflowModelId;
    }

    public function description(): ModflowModelDescription
    {
        if ($this->description === null){
            $this->description = ModflowModelDescription::fromString($this->payload['description']);
        }

        return $this->description;
    }

    public function userId(): UserId
    {
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }
}
