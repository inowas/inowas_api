<?php

declare(strict_types=1);

namespace Inowas\ScenarioAnalysis\Model\Command;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\ModelName;
use Inowas\Common\Modflow\ModelDescription;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class CreateScenario extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function byUserWithBaseModelAndScenarioId(
        ScenarioAnalysisId $scenarioAnalysisId,
        UserId $userId,
        ModflowId $baseModelId,
        ModflowId $scenarioId,
        ModelName $name,
        ModelDescription $description
    ): CreateScenario
    {
        return new self([
            'scenarioanalysis_id' => $scenarioAnalysisId->toString(),
            'user_id' => $userId->toString(),
            'basemodel_id' => $baseModelId->toString(),
            'scenario_id' => $scenarioId->toString(),
            'name' => $name->toString(),
            'description' => $description->toString()
        ]);
    }

    public function scenarioAnalysisId(): ScenarioAnalysisId
    {
        return ScenarioAnalysisId::fromString($this->payload['scenarioanalysis_id']);
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->payload['user_id']);
    }

    public function baseModelId(): ModflowId
    {
        return ModflowId::fromString($this->payload['basemodel_id']);
    }

    public function scenarioId(): ModflowId
    {
        return ModflowId::fromString($this->payload['scenario_id']);
    }

    public function name(): ModelName
    {
        return ModelName::fromString($this->payload['name']);
    }

    public function description(): ModelDescription
    {
        return ModelDescription::fromString($this->payload['description']);
    }
}
