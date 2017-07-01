<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Event\ModflowModel;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Soilmodel\SoilmodelId;
use Prooph\EventSourcing\AggregateChanged;

/** @noinspection LongInheritanceChainInspection */
class ModflowModelWasCloned extends AggregateChanged
{
    /** @var ModflowId */
    private $baseModelId;

    /** @var ModflowId */
    private $modelId;

    /** @var UserId */
    private $userId;

    /** @var SoilmodelId */
    private $soilmodelId;

    /** @var bool */
    private $cloneSoilmodel;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param ModflowId $baseModelId
     * @param ModflowId $modflowId
     * @param UserId $userId
     * @param SoilmodelId $soilmodelId
     * @param bool $cloneSoilmodel
     * @return ModflowModelWasCloned
     */
    public static function fromModelAndUserWithParameters(
        ModflowId $baseModelId,
        ModflowId $modflowId,
        UserId $userId,
        SoilmodelId $soilmodelId,
        bool $cloneSoilmodel
    ): ModflowModelWasCloned
    {
        $event = self::occur($modflowId->toString(),[
            'basemodel_id' => $baseModelId->toString(),
            'user_id' => $userId->toString(),
            'soilmodel_id' => $soilmodelId->toString(),
            'clone_soilmodel' => $cloneSoilmodel
        ]);

        $event->baseModelId = $baseModelId;
        $event->modelId = $modflowId;
        $event->userId = $userId;
        $event->soilmodelId = $soilmodelId;
        $event->cloneSoilmodel = $cloneSoilmodel;

        return $event;
    }

    public function baseModelId(): ModflowId
    {
        if ($this->baseModelId === null){
            $this->baseModelId = ModflowId::fromString($this->payload['basemodel_id']);
        }

        return $this->baseModelId;
    }

    public function cloneSoilmodel(): bool
    {
        if ($this->cloneSoilmodel === null){
            $this->cloneSoilmodel = $this->payload['clone_soilmodel'];
        }

        return $this->cloneSoilmodel;
    }

    public function modelId(): ModflowId
    {
        if ($this->modelId === null){
            $this->modelId = ModflowId::fromString($this->aggregateId());
        }

        return $this->modelId;
    }

    public function soilmodelId(): SoilmodelId
    {
        if ($this->soilmodelId === null){
            $this->soilmodelId = SoilmodelId::fromString($this->payload['soilmodel_id']);
        }

        return $this->soilmodelId;
    }

    public function userId(): UserId
    {
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }
}
