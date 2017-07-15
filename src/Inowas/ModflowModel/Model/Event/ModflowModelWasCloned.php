<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Event;

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

    /** @var  array */
    private $boundaries;

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
        bool $cloneSoilmodel,
        array $boundaries
    ): ModflowModelWasCloned
    {
        $event = self::occur($modflowId->toString(),[
            'basemodel_id' => $baseModelId->toString(),
            'user_id' => $userId->toString(),
            'soilmodel_id' => $soilmodelId->toString(),
            'clone_soilmodel' => $cloneSoilmodel,
            'boundaries' => $boundaries
        ]);

        $event->baseModelId = $baseModelId;
        $event->modelId = $modflowId;
        $event->userId = $userId;
        $event->soilmodelId = $soilmodelId;
        $event->cloneSoilmodel = $cloneSoilmodel;
        $event->boundaries = $boundaries;

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

    public function boundaries(): array
    {
        if ($this->boundaries === null){
            $this->boundaries = $this->payload['boundaries'];
        }

        return $this->boundaries;
    }
}
