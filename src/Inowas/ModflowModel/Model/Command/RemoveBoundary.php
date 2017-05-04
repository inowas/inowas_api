<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Command;

use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ModflowId;
use Inowas\ModflowModel\Model\ScenarioId;
use Inowas\Common\Id\UserId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class RemoveBoundary extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function fromBaseModel(UserId $userId, ModflowId $baseModelId, BoundaryId $boundaryId): RemoveBoundary
    {
        $payload = [
            'user_id' => $userId->toString(),
            'basemodel_id' => $baseModelId->toString(),
            'boundary_id' => $boundaryId->toString()
        ];

        return new self($payload);
    }

    public static function fromScenario(UserId $userId, ModflowId $baseModelId, ModflowId $scenarioId, BoundaryId $boundaryId): RemoveBoundary
    {
        $payload = [
            'user_id' => $userId->toString(),
            'basemodel_id' => $baseModelId->toString(),
            'scenario_id' => $scenarioId->toString(),
            'boundary_id' => $boundaryId->toString()
        ];

        return new self($payload);
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->payload['user_id']);
    }

    public function baseModelId(): ModflowId
    {
        return ModflowId::fromString($this->payload['basemodel_id']);
    }

    public function scenarioId(): ?ModflowId
    {
        if (array_key_exists('scenario_id', $this->payload)){
            return ModflowId::fromString($this->payload['scenario_id']);
        }

        return null;
    }

    public function boundaryId(): BoundaryId
    {
        return BoundaryId::fromString($this->payload['boundary_id']);
    }
}
