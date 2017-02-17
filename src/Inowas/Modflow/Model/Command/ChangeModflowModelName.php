<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Command;

use Inowas\Modflow\Model\ModflowId;
use Inowas\Modflow\Model\ModflowModelName;
use Inowas\Modflow\Model\UserId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class ChangeModflowModelName extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function forModflowModel(UserId $userId, ModflowId $modelId, ModflowModelName $modelName): ChangeModflowModelName
    {
        return new self(
            [
                'user_id' => $userId->toString(),
                'modflow_model_id' => $modelId->toString(),
                'name' => $modelName->toString()
            ]
        );
    }

    public static function forScenario(UserId $userId, ModflowId $modelId, ModflowId $scenarioId, ModflowModelName $modelName): ChangeModflowModelName
    {
        return new self(
            [
                'user_id' => $userId->toString(),
                'modflow_model_id' => $modelId->toString(),
                'scenario_id' => $scenarioId->toString(),
                'name' => $modelName->toString()
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

    public function name(): ModflowModelName
    {
        return ModflowModelName::fromString($this->payload['name']);
    }

    public function scenarioId(): ?ModflowId
    {
        if (array_key_exists('scenario_id', $this->payload)){
            return ModflowId::fromString($this->payload['scenario_id']);
        }

        return null;
    }
}
