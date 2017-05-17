<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Command;

use Inowas\Common\Boundaries\Area;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\TimeUnit;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class CreateModflowModel extends Command implements PayloadConstructable
{

    use PayloadTrait;

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

    public function userId(): UserId
    {
        return UserId::fromString($this->payload['user_id']);
    }

    public function modflowModelId(): ModflowId
    {
        return ModflowId::fromString($this->payload['modflowmodel_id']);
    }

    public function area(): Area
    {
        return unserialize($this->payload['area']);
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
