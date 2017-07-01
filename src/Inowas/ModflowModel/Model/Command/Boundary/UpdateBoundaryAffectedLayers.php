<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Command\Boundary;

use Inowas\Common\Grid\AffectedLayers;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class UpdateBoundaryAffectedLayers extends Command implements PayloadConstructable
{

    use PayloadTrait;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param UserId $userId
     * @param ModflowId $modelId
     * @param BoundaryId $boundaryId
     * @param AffectedLayers $affectedLayers
     * @return UpdateBoundaryAffectedLayers
     */
    public static function byUserModelAndBoundary(
        UserId $userId,
        ModflowId $modelId,
        BoundaryId $boundaryId,
        AffectedLayers $affectedLayers
    ): UpdateBoundaryAffectedLayers
    {
        $payload = [
            'user_id' => $userId->toString(),
            'model_id' => $modelId->toString(),
            'boundary_id' => $boundaryId->toString(),
            'affected_layers' => $affectedLayers->toArray()
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

    public function affectedLayers(): AffectedLayers
    {
        return AffectedLayers::fromArray($this->payload['affected_layers']);
    }
}
