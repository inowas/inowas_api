<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Event;

use Inowas\Common\Boundaries\Area;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\ModelDescription;
use Inowas\Common\Modflow\ModelName;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Common\Soilmodel\SoilmodelId;
use Prooph\EventSourcing\AggregateChanged;

/** @noinspection LongInheritanceChainInspection */
class ModflowModelWasCloned extends AggregateChanged
{
    /** @var ModflowId */
    private $baseModelId;

    /** @var UserId */
    private $baseModelUserId;

    /** @var ModflowId */
    private $modelId;

    /** @var UserId */
    private $userId;

    /** @var ModelName */
    private $name;

    /** @var ModelDescription */
    private $description;

    /** @var Area */
    private $area;

    /** @var SoilmodelId */
    private $soilmodelId;

    /** @var array */
    private $boundaries;

    /** @var  GridSize */
    private $gridSize;

    /** @var  BoundingBox */
    private $boundingBox;

    /** @var  LengthUnit */
    private $lengthUnit;

    /** @var  TimeUnit */
    private $timeUnit;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param ModflowId $baseModelId
     * @param UserId $baseModelUserId
     * @param ModflowId $modflowId
     * @param UserId $userId
     * @param ModelName $name
     * @param ModelDescription $description
     * @param SoilmodelId $soilmodelId
     * @param Area $area
     * @param array $boundaries
     * @param GridSize $gridSize
     * @param BoundingBox $boundingBox
     * @param LengthUnit $lengthUnit
     * @param TimeUnit $timeUnit
     * @return ModflowModelWasCloned
     */
    public static function fromModelAndUserWithParameters(
        ModflowId $baseModelId,
        UserId $baseModelUserId,
        ModflowId $modflowId,
        UserId $userId,
        ModelName $name,
        ModelDescription $description,
        SoilmodelId $soilmodelId,
        Area $area,
        array $boundaries,
        GridSize $gridSize,
        BoundingBox $boundingBox,
        LengthUnit $lengthUnit,
        TimeUnit $timeUnit
    ): ModflowModelWasCloned
    {
        $event = self::occur($modflowId->toString(),[
            'basemodel_id' => $baseModelId->toString(),
            'basemodel_user_id' => $baseModelUserId->toString(),
            'user_id' => $userId->toString(),
            'name' => $name->toString(),
            'description' => $description->toString(),
            'area' => serialize($area),
            'soilmodel_id' => $soilmodelId->toString(),
            'grid_size' => $gridSize->toArray(),
            'bounding_box' => $boundingBox->toArray(),
            'length_unit' => $lengthUnit->toInt(),
            'time_unit' => $timeUnit->toInt(),
            'boundaries' => $boundaries
        ]);

        $event->baseModelId = $baseModelId;
        $event->baseModelUserId = $baseModelUserId;
        $event->modelId = $modflowId;
        $event->userId = $userId;
        $event->name = $name;
        $event->description = $description;
        $event->area = $area;
        $event->soilmodelId = $soilmodelId;
        $event->gridSize = $gridSize;
        $event->boundingBox = $boundingBox;
        $event->lengthUnit = $lengthUnit;
        $event->timeUnit = $timeUnit;

        return $event;
    }


    public function baseModelId(): ModflowId
    {
        if ($this->baseModelId === null){
            $this->baseModelId = ModflowId::fromString($this->payload['basemodel_id']);
        }

        return $this->baseModelId;
    }

    public function baseModelUserId(): UserId
    {
        if ($this->baseModelUserId === null){
            $this->baseModelUserId = ModflowId::fromString($this->payload['basemodel_user_id']);
        }

        return $this->baseModelUserId;
    }

    public function modelId(): ModflowId
    {
        if ($this->modelId === null){
            $this->modelId = ModflowId::fromString($this->aggregateId());
        }

        return $this->modelId;
    }

    public function name(): ModelName
    {
        if ($this->name === null) {
            $this->name = ModelName::fromString($this->payload['name']);
        }

        return $this->name;
    }

    public function description(): ModelDescription
    {
        if ($this->description === null) {
            $this->description = ModelDescription::fromString($this->payload['description']);
        }

        return $this->description;
    }

    public function area(): Area
    {
        if ($this->area === null){
            $this->area = unserialize($this->payload['area'], [Area::class]);
        }

        return $this->area;
    }

    public function soilmodelId(): SoilmodelId
    {
        if ($this->soilmodelId === null){
            $this->soilmodelId = SoilmodelId::fromString($this->payload['soilmodel_id']);
        }

        return $this->soilmodelId;
    }

    public function boundaryIds(): array
    {
        if ($this->boundaries === null) {
            $this->boundaries = $this->payload['boundaries'];
        }

        return $this->boundaries;
    }

    public function gridSize(): GridSize
    {
        if ($this->gridSize === null){
            $this->gridSize = GridSize::fromArray($this->payload['grid_size']);
        }

        return $this->gridSize;
    }

    public function boundingBox(): BoundingBox
    {
        if ($this->boundingBox === null){
            $this->boundingBox = BoundingBox::fromArray($this->payload['bounding_box']);
        }

        return $this->boundingBox;
    }

    public function userId(): UserId
    {
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }

    public function lengthUnit(): LengthUnit
    {
        if ($this->lengthUnit === null){
            $this->lengthUnit = LengthUnit::fromInt($this->payload['length_unit']);
        }

        return $this->lengthUnit;
    }

    public function timeUnit(): TimeUnit
    {
        if ($this->timeUnit === null){
            $this->timeUnit = TimeUnit::fromInt($this->payload['time_unit']);
        }

        return $this->timeUnit;
    }
}
