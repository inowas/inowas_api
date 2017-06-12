<?php

declare(strict_types=1);

namespace Inowas\ScenarioAnalysis\Model\Command;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class CloneScenario extends Command implements PayloadConstructable
{

    use PayloadTrait;

    /**
     * @param UserId $userId
     * @param ScenarioAnalysisId $scenarioAnalysisId
     * @param ModflowId $scenarioId
     * @param ModflowId $newScenarioId
     * @return CloneScenario
     */
    public static function byUserWithId(
        UserId $userId,
        ScenarioAnalysisId $scenarioAnalysisId,
        ModflowId $scenarioId,
        ModflowId $newScenarioId
    ): CloneScenario
    {
        return new self([
            'base_scenario_id' => $scenarioId->toString(),
            'new_scenario_id' => $newScenarioId->toString(),
            'scenarioanalysis_id' => $scenarioAnalysisId->toString(),
            'user_id' => $userId->toString(),
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

    public function baseScenarioId(): ModflowId
    {
        return ModflowId::fromString($this->payload['base_scenario_id']);
    }

    public function newScenarioId(): ModflowId
    {
        return ModflowId::fromString($this->payload['new_scenario_id']);
    }
}
