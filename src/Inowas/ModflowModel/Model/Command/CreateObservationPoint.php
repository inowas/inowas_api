<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Command;

use Inowas\Common\Boundaries\ObservationPointName;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\ObservationPointId;
use Inowas\Common\Id\UserId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class CreateObservationPoint extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function byUserModelIdBoundaryId(
        UserId $userId,
        ModflowId $modelId,
        BoundaryId $boundaryId,
        ObservationPointId $observationPointId,
        ObservationPointName $observationPointName,
        Geometry $geometry
    ): CreateObservationPoint
    {
        $payload = [
            'user_id' => $userId->toString(),
            'model_id' => $modelId->toString(),
            'boundary_id' => $boundaryId->toString(),
            'observation_point_id' => $observationPointId->toString(),
            'observation_point_name' => $observationPointName->toString(),
            'observation_point_geometry' => $geometry->toArray()
        ];

        return new self($payload);
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->payload['user_id']);
    }

    public function modelId(): ModflowId
    {
        return ModflowId::fromString($this->payload['model_id']);
    }

    public function boundaryId(): BoundaryId
    {
        return BoundaryId::fromString($this->payload['boundary_id']);
    }

    public function observationPointId(): ObservationPointId
    {
        return ObservationPointId::fromString($this->payload['observation_point_id']);
    }

    public function observationPointName(): ObservationPointName
    {
        return ObservationPointName::fromString($this->payload['observation_point_name']);
    }

    public function geometry(): Geometry
    {
        return Geometry::fromArray($this->payload['observation_point_geometry']);
    }
}
