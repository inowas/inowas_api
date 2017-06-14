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
class ModflowModelWasCreated extends AggregateChanged
{

    /** @var ModflowId */
    private $modelId;

    /** @var UserId */
    private $userId;

    /** @var SoilmodelId */
    private $soilmodelId;

    /** @var ModelName */
    private $name;

    /** @var ModelDescription */
    private $description;

    /** @var Area */
    private $area;

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

    /** @var  ModflowId */
    protected $calculationId;

    /** @noinspection MoreThanThreeArgumentsInspection
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
     * @param ModflowId $calculationId
     * @return ModflowModelWasCreated
     */
    public static function withParameters(
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
        TimeUnit $timeUnit,
        ModflowId $calculationId
    ): ModflowModelWasCreated
    {
        $event = self::occur($modflowId->toString(),[
            'user_id' => $userId->toString(),
            'soilmodel_id' => $soilmodelId->toString(),
            'name' => $name->toString(),
            'description' => $description->toString(),
            'area' => serialize($area),
            'grid_size' => $gridSize->toArray(),
            'bounding_box' => $boundingBox->toArray(),
            'length_unit' => $lengthUnit->toInt(),
            'time_unit' => $timeUnit->toInt(),
            'boundaries' => $boundaries,
            'calculation_id' => $calculationId->toString()
        ]);

        $event->modelId = $modflowId;
        $event->userId = $userId;
        $event->area = $area;
        $event->gridSize = $gridSize;
        $event->boundingBox = $boundingBox;
        $event->lengthUnit = $lengthUnit;
        $event->timeUnit = $timeUnit;
        $event->soilmodelId = $soilmodelId;
        $event->calculationId = $calculationId;

        return $event;
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

    public function boundaries(): array
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

    public function calculationId(): ModflowId
    {
        if ($this->calculationId === null){
            $this->calculationId = ModflowId::fromString($this->payload['calculation_id']);
        }

        return $this->calculationId;
    }
}
