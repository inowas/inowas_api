<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Command;

use Inowas\Common\Boundaries\ModflowBoundary;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class UpdateBoundary extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function byUserAndModel(
        UserId $userId,
        ModflowId $baseModelId,
        ModflowBoundary $boundary
    ): UpdateBoundary
    {
        $payload = [
            'user_id' => $userId->toString(),
            'basemodel_id' => $baseModelId->toString(),
            'boundary' => serialize($boundary)
        ];

        return new self($payload);
    }

    public function boundary(): ModflowBoundary
    {
        return unserialize($this->payload['boundary']);
    }

    public function baseModelId(): ModflowId
    {
        return ModflowId::fromString($this->payload['basemodel_id']);
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->payload['user_id']);
    }
}
