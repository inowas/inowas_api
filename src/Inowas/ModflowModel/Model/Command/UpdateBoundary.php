<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Command;

use Inowas\Common\Boundaries\BoundaryFactory;
use Inowas\Common\Boundaries\ModflowBoundary;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class UpdateBoundary extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function forModflowModel(UserId $userId, ModflowId $modelId, BoundaryId $boundaryId, ModflowBoundary $boundary): UpdateBoundary
    {
        return new self(
            [
                'user_id' => $userId->toString(),
                'model_id' => $modelId->toString(),
                'boundary_id' => $boundaryId->toString(),
                'boundary' => $boundary->toArray()
            ]
        );
    }

    public function modflowModelId(): ModflowId
    {
        return ModflowId::fromString($this->payload['model_id']);
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->payload['user_id']);
    }

    public function boundaryId(): BoundaryId
    {
        return BoundaryId::fromString($this->payload['boundary_id']);
    }

    public function boundary(): ModflowBoundary
    {
        return BoundaryFactory::createFromArray($this->payload['boundary']);
    }
}
