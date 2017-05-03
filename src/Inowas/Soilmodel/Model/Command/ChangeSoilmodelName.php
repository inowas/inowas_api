<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model\Command;

use Inowas\Common\Id\UserId;
use Inowas\Common\Soilmodel\SoilmodelId;
use Inowas\Common\Soilmodel\SoilmodelName;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class ChangeSoilmodelName extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function forSoilmodel(UserId $userId, SoilmodelId $soilmodelId, SoilmodelName $name): ChangeSoilmodelName
    {
        return new self(
            [
                'user_id' => $userId->toString(),
                'soilmodel_id' => $soilmodelId->toString(),
                'name' => $name->toString()
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

    public function name(): SoilmodelName
    {
        return SoilmodelName::fromString($this->payload['name']);
    }
}
