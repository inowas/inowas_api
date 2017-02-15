<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Command;

use Inowas\Modflow\Model\ModflowBoundary;
use Inowas\Modflow\Model\ModflowId;
use Inowas\Modflow\Model\UserId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class AddBoundary extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function toBaseModel(
        UserId $userId,
        ModflowId $baseModelId,
        ModflowBoundary $boundary
    ): AddBoundary
    {
        $payload = [
            'user_id' => $userId->toString(),
            'basemodel_id' => $baseModelId->toString(),
            'boundary' => serialize($boundary)
        ];

        return new self($payload);
    }

    public static function toScenario(
        UserId $userId,
        ModflowId $baseModelId,
        ModflowId $scenarioId,
        ModflowBoundary $boundary
    ): AddBoundary
    {
        $payload = [
            'user_id' => $userId->toString(),
            'basemodel_id' => $baseModelId->toString(),
            'scenario_id' => $scenarioId->toString(),
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

    public function scenarioId(): ?ModflowId
    {
        if (array_key_exists('scenario_id', $this->payload)){
            return ModflowId::fromString($this->payload['scenario_id']);
        }

        return null;
    }
}
