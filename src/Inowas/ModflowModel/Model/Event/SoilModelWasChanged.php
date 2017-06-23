<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Event;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Soilmodel\SoilmodelId;
use Prooph\EventSourcing\AggregateChanged;

/** @noinspection LongInheritanceChainInspection */
class SoilModelWasChanged extends AggregateChanged
{

    /** @var ModflowId */
    private $modflowModelId;

    /** @var SoilmodelId */
    private $soilmodelId;

    /** @var UserId */
    private $userId;

    public static function withSoilmodelId(UserId $userId, ModflowId $modflowModelId, SoilmodelId $newSoilModelId): SoilModelWasChanged
    {
        $event = self::occur(
            $modflowModelId->toString(), [
                'user_id' => $userId->toString(),
                'soilmodel_id' => $newSoilModelId->toString()
            ]
        );

        $event->modflowModelId = $modflowModelId;
        $event->soilmodelId = $newSoilModelId;

        return $event;
    }

    public function userId(): UserId
    {
        if ($this->userId === null) {
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }

    public function modflowModelId(): ModflowId
    {
        if ($this->modflowModelId === null){
            $this->modflowModelId = ModflowId::fromString($this->aggregateId());
        }

        return $this->modflowModelId;
    }

    public function soilModelId(): SoilmodelId
    {
        if ($this->soilmodelId === null){
            $this->soilmodelId = SoilmodelId::fromString($this->payload['soilmodel_id']);
        }

        return $this->soilmodelId;
    }
}
