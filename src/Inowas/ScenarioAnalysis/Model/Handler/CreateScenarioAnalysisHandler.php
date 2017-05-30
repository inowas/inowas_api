<?php

declare(strict_types=1);

namespace Inowas\ScenarioAnalysis\Model\Handler;

use Inowas\ScenarioAnalysis\Model\Command\CreateScenarioAnalysis;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisAggregate;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisList;

final class CreateScenarioAnalysisHandler
{

    private $scenarioAnalysisList;

    public function __construct(ScenarioAnalysisList $scenarioAnalysisList)
    {
        $this->scenarioAnalysisList = $scenarioAnalysisList;
    }

    public function __invoke(CreateScenarioAnalysis $command)
    {
        $scenarioAnalysis = ScenarioAnalysisAggregate::create(
            $command->scenarioAnalysisId(),
            $command->userId(),
            $command->baseModelId(),
            $command->baseModelCalculationId(),
            $command->name(),
            $command->description()
        );

        $this->scenarioAnalysisList->add($scenarioAnalysis);
    }
}
