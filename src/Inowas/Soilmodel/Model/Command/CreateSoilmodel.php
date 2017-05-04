<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model\Command;

use Inowas\Common\Id\UserId;
use Inowas\Common\Soilmodel\SoilmodelId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class CreateSoilmodel extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function byUserWithModelId(UserId $userId, SoilmodelId $id): CreateSoilmodel
    {
        return new self(
            [
                'user_id' => $userId->toString(),
                'soilmodel_id' => $id->toString()
            ]
        );
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->payload['user_id']);
    }

    public function soilmodelId(): SoilmodelId
    {
        return SoilmodelId::fromString($this->payload['soilmodel_id']);
    }
}
