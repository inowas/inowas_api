<?php

declare(strict_types=1);

namespace Inowas\ScenarioAnalysis\Model\Handler;

use Inowas\ScenarioAnalysis\Infrastructure\Projection\ScenarioAnalysisFinder;
use Inowas\ScenarioAnalysis\Model\Command\CreateScenario;
use Inowas\ScenarioAnalysis\Model\Exception\ScenarioAnalysisNotFoundException;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisAggregate;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisList;

final class CreateScenarioHandler
{
    /** @var ScenarioAnalysisFinder */
    private $finder;

    /** @var ScenarioAnalysisList  */
    private $scenarioAnalysisList;

    public function __construct(ScenarioAnalysisList $scenarioAnalysisList, ScenarioAnalysisFinder $finder)
    {
        $this->scenarioAnalysisList = $scenarioAnalysisList;
        $this->finder = $finder;
    }

    public function __invoke(CreateScenario $command)
    {
        /** @var ScenarioAnalysisAggregate $scenarioAnalysis */
        $scenarioAnalysis = $this->scenarioAnalysisList->get($command->scenarioAnalysisId());

        if (! $scenarioAnalysis) {
            throw ScenarioAnalysisNotFoundException::withId($command->scenarioAnalysisId());
        }

        $scenarioAnalysis->createScenario(
            $command->userId(),
            $command->scenarioId(),
            $command->baseModelId()
        );

        $this->scenarioAnalysisList->save($scenarioAnalysis);
    }
}
