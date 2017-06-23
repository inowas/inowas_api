<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Event;

use Inowas\Common\Geometry\Polygon;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
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

    /** @var  Polygon */
    private $polygon;

    /** @var  GridSize */
    private $gridSize;

    /** @var  BoundingBox */
    private $boundingBox;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param ModflowId $modflowId
     * @param UserId $userId
     * @param Polygon $polygon
     * @param GridSize $gridSize
     * @param BoundingBox $boundingBox
     * @param SoilmodelId $soilmodelId
     * @return ModflowModelWasCreated
     */
    public static function withParameters(
        ModflowId $modflowId,
        UserId $userId,
        Polygon $polygon,
        GridSize $gridSize,
        BoundingBox $boundingBox,
        SoilmodelId $soilmodelId
    ): ModflowModelWasCreated
    {
        $event = self::occur($modflowId->toString(),[
            'user_id' => $userId->toString(),
            'soilmodel_id' => $soilmodelId->toString(),
            'polygon' => $polygon->toJson(),
            'grid_size' => json_encode($gridSize),
            'bounding_box' => json_encode($boundingBox)
        ]);

        $event->modelId = $modflowId;
        $event->userId = $userId;
        $event->soilmodelId = $soilmodelId;
        $event->polygon = $polygon;
        $event->gridSize = $gridSize;

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

    public function userId(): UserId
    {
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }

    public function boundingBox(): BoundingBox
    {
        if ($this->boundingBox === null){
            $this->boundingBox = BoundingBox::fromArray(json_decode($this->payload['bounding_box'], true));
        }

        return $this->boundingBox;
    }

    public function gridSize(): GridSize
    {
        if ($this->gridSize === null){
            $this->gridSize = GridSize::fromArray(json_decode($this->payload['grid_size'], true));
        }

        return $this->gridSize;
    }

    public function polygon(): Polygon
    {
        if ($this->polygon === null) {
            $this->polygon = Polygon::fromJson($this->payload['polygon']);
        }

        return $this->polygon;
    }
}
