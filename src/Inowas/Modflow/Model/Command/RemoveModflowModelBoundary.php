<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Command;

use Inowas\Modflow\Model\BoundaryId;
use Inowas\Modflow\Model\ModflowModelId;
use Inowas\Modflow\Model\ScenarioId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class RemoveModflowModelBoundary extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function forModflowModel(ModflowModelId $modelId, BoundaryId $boundaryId): RemoveModflowModelBoundary
    {
        $payload = [
            'modflow_model_id' => $modelId->toString(),
            'boundary_id' => $boundaryId->toString()
        ];

        return new self($payload);
    }

    public static function forModflowScenario(ModflowModelId $modelId, ScenarioId $scenarioId, BoundaryId $boundaryId): RemoveModflowModelBoundary
    {
        $payload = [
            'modflow_model_id' => $modelId->toString(),
            'boundary_id' => $boundaryId->toString(),
            'scenario_id' => $scenarioId->toString()
        ];

        return new self($payload);
    }

    public function modflowModelId(): ModflowModelId
    {
        return ModflowModelId::fromString($this->payload['modflow_model_id']);
    }

    public function boundaryId(): BoundaryId
    {
        return BoundaryId::fromString($this->payload['boundary_id']);
    }

    public function scenarioId(): ?ScenarioId
    {
        if (array_key_exists('scenario_id', $this->payload)){
            return ScenarioId::fromString($this->payload['scenario_id']);
        }

        return null;
    }
}
