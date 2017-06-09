<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Command;

use Inowas\Common\Boundaries\Area;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\ModelDescription;
use Inowas\Common\Modflow\ModelName;
use Inowas\Common\Modflow\TimeUnit;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class CreateModflowModel extends Command implements PayloadConstructable
{

    use PayloadTrait;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param UserId $userId
     * @param ModflowId $modelId
     * @param Area $area
     * @param GridSize $gridSize
     * @return CreateModflowModel
     */
    public static function newWithId(UserId $userId, ModflowId $modelId, Area $area, GridSize $gridSize): CreateModflowModel
    {
        return new self(
            [
                'user_id' => $userId->toString(),
                'modflowmodel_id' => $modelId->toString(),
                'area' => serialize($area),
                'grid_size' => $gridSize->toArray()
            ]
        );
    }

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param UserId $userId
     * @param ModflowId $modelId
     * @param Area $area
     * @param GridSize $gridSize
     * @param TimeUnit $timeUnit
     * @param LengthUnit $lengthUnit
     * @return CreateModflowModel
     */
    public static function newWithIdAndUnits(UserId $userId, ModflowId $modelId, Area $area, GridSize $gridSize, TimeUnit $timeUnit, LengthUnit $lengthUnit): CreateModflowModel
    {
        return new self(
            [
                'user_id' => $userId->toString(),
                'modflowmodel_id' => $modelId->toString(),
                'area' => serialize($area),
                'grid_size' => $gridSize->toArray(),
                'time_unit' => $timeUnit->toValue(),
                'length_unit' => $lengthUnit->toValue()
            ]
        );
    }

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param UserId $userId
     * @param ModflowId $modelId
     * @param ModelName $name
     * @param ModelDescription $description
     * @param Area $area
     * @param GridSize $gridSize
     * @param TimeUnit $timeUnit
     * @param LengthUnit $lengthUnit
     * @return CreateModflowModel
     */
    public static function newWithIdNameDescriptionAndUnits(UserId $userId, ModflowId $modelId, ModelName $name, ModelDescription $description, Area $area, GridSize $gridSize, TimeUnit $timeUnit, LengthUnit $lengthUnit): CreateModflowModel
    {
        return new self(
            [
                'user_id' => $userId->toString(),
                'modflowmodel_id' => $modelId->toString(),
                'name' => $name->toString(),
                'description' => $description->toString(),
                'area' => serialize($area),
                'grid_size' => $gridSize->toArray(),
                'time_unit' => $timeUnit->toValue(),
                'length_unit' => $lengthUnit->toValue()
            ]
        );
    }



    public function userId(): UserId
    {
        return UserId::fromString($this->payload['user_id']);
    }

    public function modflowModelId(): ModflowId
    {
        return ModflowId::fromString($this->payload['modflowmodel_id']);
    }

    public function name(): ModelName
    {
        if (array_key_exists('name', $this->payload)) {
            return ModelName::fromString($this->payload['name']);
        }

        return ModelName::fromString('');
    }

    public function description(): ModelDescription
    {
        if (array_key_exists('description', $this->payload)) {
            return ModelDescription::fromString($this->payload['description']);
        }

        return ModelDescription::fromString('');
    }

    public function area(): Area
    {
        return unserialize($this->payload['area'], [Area::class]);
    }

    public function gridSize(): GridSize
    {
        return GridSize::fromArray($this->payload['grid_size']);
    }

    public function timeUnit(): TimeUnit
    {
        if (! array_key_exists('time_unit', $this->payload)){
            return TimeUnit::fromInt(TimeUnit::DAYS);
        }

        return TimeUnit::fromValue($this->payload['time_unit']);
    }

    public function lengthUnit(): LengthUnit
    {
        if (! array_key_exists('length_unit', $this->payload)){
            return LengthUnit::fromInt(LengthUnit::METERS);
        }

        return LengthUnit::fromValue($this->payload['length_unit']);
    }
}
