<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model\Command;

use Inowas\Common\Id\UserId;
use Inowas\Common\Soilmodel\SoilmodelId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class CloneSoilmodel extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function byUserWithModelId(SoilmodelId $newId, UserId $userId, SoilmodelId $fromId): CloneSoilmodel
    {
        return new self([
            'new_id' => $newId->toString(),
            'user_id' => $userId->toString(),
            'origin_id' => $fromId->toString()
        ]);
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->payload['user_id']);
    }

    public function newSoilmodelId(): SoilmodelId
    {
        return SoilmodelId::fromString($this->payload['new_id']);
    }

    public function fromSoilmodelId(): SoilmodelId
    {
        return SoilmodelId::fromString($this->payload['origin_id']);
    }
}
