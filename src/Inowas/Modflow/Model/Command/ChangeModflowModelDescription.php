<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Command;

use Inowas\Modflow\Model\ModflowModelDescription;
use Inowas\Modflow\Model\ModflowId;
use Inowas\Modflow\Model\UserId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class ChangeModflowModelDescription extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function forModflowModel(UserId $userId, ModflowId $modelId, ModflowModelDescription $description): ChangeModflowModelDescription
    {
        return new self(
            [
                'user_id' => $userId->toString(),
                'modflow_model_id' => $modelId->toString(),
                'description' => $description->toString()
            ]
        );
    }

    public static function forScenario(UserId $userId, ModflowId $modelId, ModflowId $scenarioId, ModflowModelDescription $description): ChangeModflowModelDescription
    {
        return new self(
            [
                'user_id' => $userId->toString(),
                'modflow_model_id' => $modelId->toString(),
                'scenario_id' => $scenarioId->toString(),
                'description' => $description->toString()
            ]
        );
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->payload['user_id']);
    }

    public function modflowModelId(): ModflowId
    {
        return ModflowId::fromString($this->payload['modflow_model_id']);
    }

    public function description(): ModflowModelDescription
    {
        return ModflowModelDescription::fromString($this->payload['description']);
    }

    public function scenarioId(): ?ModflowId
    {
        if (array_key_exists('scenario_id', $this->payload)){
            return ModflowId::fromString($this->payload['scenario_id']);
        }

        return null;
    }
}
