<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Command\Boundary;

use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class UpdateBoundaryGeometry extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function byUser(
        UserId $userId,
        ModflowId $baseModelId,
        BoundaryId $boundaryId,
        Geometry $geometry
    ): UpdateBoundaryGeometry
    {
        $payload = [
            'user_id' => $userId->toString(),
            'basemodel_id' => $baseModelId->toString(),
            'boundary_id' => $boundaryId->toString(),
            'geometry' => serialize($geometry)
        ];

        return new self($payload);
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->payload['user_id']);
    }

    public function modelId(): ModflowId
    {
        return ModflowId::fromString($this->payload['basemodel_id']);
    }

    public function boundaryId(): BoundaryId
    {
        return BoundaryId::fromString($this->payload['boundary_id']);
    }

    public function geometry(): Geometry
    {
        return unserialize($this->payload['geometry'], [Geometry::class]);
    }
}
