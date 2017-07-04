<?php

declare(strict_types=1);

namespace Inowas\ModflowBoundary\Model\Command;

use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ModflowId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class CloneBoundary extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function withIds(BoundaryId $existentId, BoundaryId $cloneId, ModflowId $modelId): CloneBoundary
    {
        return new self([
            'existent_id' => $existentId->toString(),
            'clone_id' => $cloneId->toString(),
            'model_id' => $modelId->toString()
        ]);
    }

    public function existentId(): BoundaryId
    {
        return BoundaryId::fromString($this->payload['existent_id']);
    }

    public function cloneId(): BoundaryId
    {
        return BoundaryId::fromString($this->payload['clone_id']);
    }

    public function modelId(): ModflowId
    {
        return ModflowId::fromString($this->payload['model_id']);
    }
}
