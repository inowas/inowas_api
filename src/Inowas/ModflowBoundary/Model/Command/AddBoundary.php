<?php

declare(strict_types=1);

namespace Inowas\ModflowBoundary\Model\Command;

use Inowas\Common\Boundaries\ModflowBoundary;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class AddBoundary extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function to(
        ModflowId $modelId,
        UserId $userId,
        ModflowBoundary $boundary
    ): AddBoundary
    {
        $payload = [
            'model_id' => $modelId->toString(),
            'user_id' => $userId->toString(),
            'boundary' => serialize($boundary)
        ];

        return new self($payload);
    }

    public function boundary(): ModflowBoundary
    {
        return unserialize($this->payload['boundary']);
    }

    public function modelId(): ModflowId
    {
        return ModflowId::fromString($this->payload['model_id']);
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->payload['user_id']);
    }
}
