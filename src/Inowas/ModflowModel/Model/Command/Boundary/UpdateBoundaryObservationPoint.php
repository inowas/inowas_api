<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Command\Boundary;

use Inowas\Common\Boundaries\ObservationPoint;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class UpdateBoundaryObservationPoint extends Command implements PayloadConstructable
{

    use PayloadTrait;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param UserId $userId
     * @param ModflowId $modelId
     * @param BoundaryId $boundaryId
     * @param ObservationPoint $observationPoint
     * @return UpdateBoundaryObservationPoint
     */
    public static function byUserModelIdBoundaryId(
        UserId $userId,
        ModflowId $modelId,
        BoundaryId $boundaryId,
        ObservationPoint $observationPoint
    ): UpdateBoundaryObservationPoint
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

    public function boundaryId(): BoundaryId
    {
        return BoundaryId::fromString($this->payload['boundary_id']);
    }

    public function observationPoint(): ObservationPoint
    {
        return unserialize($this->payload['observation_point'], [ObservationPoint::class]);
    }
}
