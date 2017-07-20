<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Command;

use Inowas\Common\Geometry\Polygon;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\Description;
use Inowas\Common\Modflow\Name;
use Inowas\Common\Modflow\TimeUnit;
use Prooph\Common\Messaging\Command;

class CreateModflowModel extends Command
{

    /** @var array */
    private $payload;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param UserId $userId
     * @param ModflowId $modelId
     * @param Name $name
     * @param Description $description
     * @param Polygon $polygon
     * @param GridSize $gridSize
     * @param TimeUnit $timeUnit
     * @param LengthUnit $lengthUnit
     * @return CreateModflowModel
     */
    public static function newWithAllParams(
        UserId $userId,
        ModflowId $modelId,
        Name $name,
        Description $description,
        Polygon $polygon,
        GridSize $gridSize,
        TimeUnit $timeUnit,
        LengthUnit $lengthUnit
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
                'length_unit' => $lengthUnit->toValue()
            ]
        );
    }

    final public function __construct(array $payload = null)
    {
        $this->setPayload($payload);
        $this->init();
    }

    public function payload(): array
    {
        return $this->payload;
    }

    protected function setPayload(array $payload): void
    {
        $this->payload = $payload;
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->payload['user_id']);
    }

    public function modelId(): ModflowId
    {
        return ModflowId::fromString($this->payload['modflowmodel_id']);
    }

    public function name(): Name
    {
        return Name::fromString($this->payload['name']);
    }

    public function description(): Description
    {
        return Description::fromString($this->payload['description']);
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
}
