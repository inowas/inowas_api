<?php

declare(strict_types=1);

namespace Inowas\ScenarioAnalysis\Model\Command;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisDescription;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisId;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisName;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class CreateScenarioAnalysis extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function byUserWithBaseModelNameAndDescription(
        ScenarioAnalysisId $scenarioAnalysisId,
        UserId $userId,
        ModflowId $baseModelId,
        ModflowId $baseModelCalculationId,
        ScenarioAnalysisName $name,
        ScenarioAnalysisDescription $description
    ): CreateScenarioAnalysis
    {
        return new self([
            'scenarioanalysis_id' => $scenarioAnalysisId->toString(),
            'user_id' => $userId->toString(),
            'basemodel_id' => $baseModelId->toString(),
            'basemodel_calculation_id' => $baseModelCalculationId->toString(),
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

    public function name(): ScenarioAnalysisName
    {
        return ScenarioAnalysisName::fromString($this->payload['name']);
    }

    public function description(): ScenarioAnalysisDescription
    {
        return ScenarioAnalysisDescription::fromString($this->payload['description']);
    }

    public function baseModelCalculationId(): ModflowId
    {
        return ModflowId::fromString($this->payload['basemodel_calculation_id']);
    }
}
