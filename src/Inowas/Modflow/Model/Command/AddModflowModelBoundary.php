<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Command;

use Inowas\Modflow\Model\ModflowBoundary;
use Inowas\Modflow\Model\ModflowModelId;
use Inowas\Modflow\Model\ScenarioId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class AddModflowModelBoundary extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function forModflowModel(
        ModflowModelId $modelId,
        ModflowBoundary $boundary
    ): AddModflowModelBoundary
    {
        $payload = [
            'modflow_model_id' => $modelId->toString(),
            'boundary' => serialize($boundary)
        ];

        return new self($payload);
    }

    public static function forModflowScenario(
        ModflowModelId $modelId,
        ScenarioId $scenarioId,
        ModflowBoundary $boundary
    ): AddModflowModelBoundary
    {
        $payload = [
            'modflow_model_id' => $modelId->toString(),
            'scenario_id' => $scenarioId->toString(),
            'boundary' => serialize($boundary)
        ];

        return new self($payload);
    }

    public function modflowModelId(): ModflowModelId
    {
        return ModflowModelId::fromString($this->payload['modflow_model_id']);
    }

    public function boundary(): ModflowBoundary
    {
        return unserialize($this->payload['boundary']);
    }

    public function scenarioId(): ?ScenarioId
    {
        if (array_key_exists('scenario_id', $this->payload)){
            return ScenarioId::fromString($this->payload['scenario_id']);
        }

        return null;
    }
}
