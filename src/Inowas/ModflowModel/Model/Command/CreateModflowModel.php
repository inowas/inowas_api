<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Command;

use Inowas\Common\Geometry\Polygon;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\ModelDescription;
use Inowas\Common\Modflow\ModelName;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Common\Soilmodel\SoilmodelId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class CreateModflowModel extends Command implements PayloadConstructable
{

    use PayloadTrait;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param UserId $userId
     * @param ModflowId $modelId
     * @param ModelName $name
     * @param ModelDescription $description
     * @param Polygon $polygon
     * @param GridSize $gridSize
     * @param TimeUnit $timeUnit
     * @param LengthUnit $lengthUnit
     * @param SoilmodelId $soilmodelId
     * @return CreateModflowModel
     */
    public static function newWithAllParams(
        UserId $userId,
        ModflowId $modelId,
        ModelName $name,
        ModelDescription $description,
        Polygon $polygon,
        GridSize $gridSize,
        TimeUnit $timeUnit,
        LengthUnit $lengthUnit,
        SoilmodelId $soilmodelId
    ): CreateModflowModel
    {
        return new self(
            [
                'user_id' => $userId->toString(),
                'modflowmodel_id' => $modelId->toString(),
                'name' => $name->toString(),
                'description' => $description->toString(),
                'polygon' => $polygon->toJson(),
                'grid_size' => $gridSize->toArray(),
                'time_unit' => $timeUnit->toValue(),
                'length_unit' => $lengthUnit->toValue(),
                'soilmodel_id' => $soilmodelId->toString()
            ]
        );
    }


    public function userId(): UserId
    {
        return UserId::fromString($this->payload['user_id']);
    }

    public function modelId(): ModflowId
    {
        return ModflowId::fromString($this->payload['modflowmodel_id']);
    }

    public function name(): ModelName
    {
        return ModelName::fromString($this->payload['name']);
    }

    public function description(): ModelDescription
    {
        return ModelDescription::fromString($this->payload['description']);
    }

    public function polygon(): Polygon
    {
        return Polygon::fromJson($this->payload['polygon']);
    }

    public function gridSize(): GridSize
    {
        return GridSize::fromArray($this->payload['grid_size']);
    }

    public function timeUnit(): TimeUnit
    {
        return TimeUnit::fromInt(TimeUnit::DAYS);
    }

    public function lengthUnit(): LengthUnit
    {
        return LengthUnit::fromInt(LengthUnit::METERS);
    }

    public function soilmodelId(): SoilmodelId
    {
        return SoilmodelId::fromString($this->payload['soilmodel_id']);
    }
}
