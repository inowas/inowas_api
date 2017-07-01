<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Command\Boundary;

use Inowas\Common\Boundaries\BoundaryName;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class UpdateBoundaryName extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function byUserModelAndBoundary(
        UserId $userId,
        ModflowId $modelId,
        BoundaryId $boundaryId,
        BoundaryName $boundaryName
    ): UpdateBoundaryName
    {
        $payload = [
            'user_id' => $userId->toString(),
            'model_id' => $modelId->toString(),
            'boundary_id' => $boundaryId->toString(),
            'boundary_name' => $boundaryName->toString()
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

    public function boundaryName(): BoundaryName
    {
        return BoundaryName::fromString($this->payload['boundary_name']);
    }
}
