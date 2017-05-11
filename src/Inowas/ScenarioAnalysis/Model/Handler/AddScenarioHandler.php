<?php

declare(strict_types=1);

namespace Inowas\ScenarioAnalysis\Model\Handler;

use Inowas\ModflowModel\Model\Command\CloneModflowModel;
use Inowas\ScenarioAnalysis\Model\Command\AddScenario;
use Inowas\ScenarioAnalysis\Model\Exception\ScenarioAnalysisNotFoundException;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisAggregate;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisList;
use Prooph\ServiceBus\CommandBus;

final class AddScenarioHandler
{

    /** @var ScenarioAnalysisList  */
    private $scenarioAnalysisList;

    /** @var  CommandBus */
    private $commandBus;

    public function __construct(ScenarioAnalysisList $scenarioAnalysisList, CommandBus $commandBus)
    {
        $this->scenarioAnalysisList = $scenarioAnalysisList;
        $this->commandBus = $commandBus;
    }

    public function __invoke(AddScenario $command)
    {
        /** @var ScenarioAnalysisAggregate $scenarioAnalysis */
        $scenarioAnalysis = $this->scenarioAnalysisList->get($command->scenarioAnalysisId());

        if (! $scenarioAnalysis) {
            throw ScenarioAnalysisNotFoundException::withId($command->scenarioAnalysisId());
        }

        $this->commandBus->dispatch(CloneModflowModel::fromBaseModel($command->baseModelId(), $command->userId(), $command->scenarioId()));
        $scenarioAnalysis->addScenario($command->userId(), $command->scenarioId());
    }
}
