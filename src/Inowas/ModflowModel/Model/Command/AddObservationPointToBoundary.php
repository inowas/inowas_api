<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Command;

use Inowas\Common\Boundaries\ModflowBoundary;
use Inowas\Common\Boundaries\ObservationPoint;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class AddObservationPointToBoundary extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function byUserModelIdBoundaryId(
        UserId $userId,
        ModflowId $modelId,
        BoundaryId $boundaryId,
        ObservationPoint $observationPoint
    ): AddObservationPointToBoundary
    {
        $payload = [
            'user_id' => $userId->toString(),
            'model_id' => $modelId->toString(),
            'boundary_id' => $boundaryId->toString(),
            'observation_point' => serialize($observationPoint)
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

    public function boundary(): ModflowBoundary
    {
        return unserialize($this->payload['boundary']);
    }

    public function observationPoint(): ObservationPoint
    {
        return unserialize($this->payload['observation_point']);
    }
}
