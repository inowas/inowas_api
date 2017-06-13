<?php

declare(strict_types=1);

namespace Inowas\ScenarioAnalysis\Model\Handler;

use Inowas\ScenarioAnalysis\Model\Command\CreateScenarioAnalysis;
use Inowas\ScenarioAnalysis\Model\Exception\ScenarioAnalysisNotFoundException;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisAggregate;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisList;

final class RemoveScenarioHandler
{

    /** @var ScenarioAnalysisList  */
    private $scenarioAnalysisList;


    public function __construct(ScenarioAnalysisList $scenarioAnalysisList)
    {
        $this->scenarioAnalysisList = $scenarioAnalysisList;
    }

    public function __invoke(CreateScenarioAnalysis $command)
    {

        /** @var ScenarioAnalysisAggregate $scenarioAnalysis */
        $scenarioAnalysis = $this->scenarioAnalysisList->get($command->scenarioAnalysisId());

        if (! $scenarioAnalysis) {
            throw ScenarioAnalysisNotFoundException::withId($command->scenarioAnalysisId());
        }

        $scenarioAnalysis->removeScenario($command->userId(), $command->baseModelId());
    }
}
