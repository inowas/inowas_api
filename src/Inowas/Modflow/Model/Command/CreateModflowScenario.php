<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Command;

use Inowas\Modflow\Model\ModflowModelId;
use Inowas\Modflow\Model\ScenarioId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class CreateModflowScenario extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function withId(ScenarioId $scenarioId, ModflowModelId $modelId): CreateModflowScenario
    {
        return new self(
            [
                'modflow_scenario_id' => $scenarioId->toString(),
                'modflow_model_id' => $modelId->toString()
            ]
        );
    }

    public function scenarioId(): ScenarioId
    {
        return ScenarioId::fromString($this->payload['modflow_scenario_id']);
    }

    public function modflowModelId(): ModflowModelId
    {
        return ModflowModelId::fromString($this->payload['modflow_model_id']);
    }
}
