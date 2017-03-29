<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model\Command;

use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Id\UserId;
use Inowas\Soilmodel\Model\SoilmodelId;
use Inowas\Soilmodel\Model\SoilmodelName;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class InterpolateSoilmodel extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function forSoilmodel(UserId $userId, SoilmodelId $soilmodelId, BoundingBox $bb, GridSize $gs): InterpolateSoilmodel
    {
        return new self(
            [
                'user_id' => $userId->toString(),
                'soilmodel_id' => $soilmodelId->toString(),
                'bounding_box' => $bb->toArray(),
                'grid_size' => $gs->toArray()
            ]
        );
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->payload['user_id']);
    }

    public function soilmodelId(): SoilmodelId
    {
        return SoilmodelId::fromString($this->payload['soilmodel_id']);
    }

    public function name(): SoilmodelName
    {
        return SoilmodelName::fromString($this->payload['name']);
    }

    public function boundingBox(): BoundingBox
    {
        return BoundingBox::fromArray($this->payload['bounding_box']);
    }

    public function gridSize(): GridSize
    {
        return GridSize::fromArray($this->payload['grid_size']);
    }
}
