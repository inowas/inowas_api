<?php

declare(strict_types=1);

namespace Inowas\ScenarioAnalysis\Model\Command;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class CreateScenarioAnalysis extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function byUserwithBaseModel(ScenarioAnalysisId $scenarioAnalysisId, UserId $userId, ModflowId $scenarioId): CreateScenarioAnalysis
    {
        return new self([
            'scenarioanalysis_id' => $scenarioAnalysisId->toString(),
            'user_id' => $userId->toString(),
            'scenario_id' => $scenarioId->toString()
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

    public function scenarioId(): ModflowId
    {
        return ModflowId::fromString($this->payload['scenario_id']);
    }
}
