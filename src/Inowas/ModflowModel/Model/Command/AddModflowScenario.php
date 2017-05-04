<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Command;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class AddModflowScenario extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function from(UserId $userId, ModflowId $baseModelId, ModflowId $scenarioId): AddModflowScenario
    {
        return new self(
            [
                'user_id' => $userId->toString(),
                'basemodel_id' => $baseModelId->toString(),
                'scenario_id' => $scenarioId->toString()
            ]
        );
    }

    public function scenarioId(): ModflowId
    {
        return ModflowId::fromString($this->payload['scenario_id']);
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
