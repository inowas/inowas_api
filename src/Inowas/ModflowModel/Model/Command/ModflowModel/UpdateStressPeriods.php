<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Command\ModflowModel;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\StressPeriods;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class UpdateStressPeriods extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function of(
        UserId $userId,
        ModflowId $modelId,
        StressPeriods $stressPeriods
    ): UpdateStressPeriods
    {
        $payload = [
            'user_id' => $userId->toString(),
            'model_id' => $modelId->toString(),
            'stressperiods' => $stressPeriods->toJson()
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

    public function stressPeriods(): StressPeriods
    {
        return StressPeriods::createFromJson($this->payload['stressperiods']);
    }
}
