<?php

declare(strict_types=1);

namespace Inowas\ScenarioAnalysis\Model\Command;

use Inowas\Common\Id\UserId;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisId;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class DeleteScenarioAnalysis extends Command implements PayloadConstructable
{

    use PayloadTrait;

    /**
     * @param UserId $userId
     * @param ScenarioAnalysisId $scenarioAnalysisId
     * @return DeleteScenarioAnalysis
     */
    public static function byUserWithId(
        UserId $userId,
        ScenarioAnalysisId $scenarioAnalysisId
    ): DeleteScenarioAnalysis
    {
        return new self([
            'scenarioanalysis_id' => $scenarioAnalysisId->toString(),
            'user_id' => $userId->toString()
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
}
