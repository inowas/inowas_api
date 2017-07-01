<?php

declare(strict_types=1);

namespace Inowas\ScenarioAnalysis\Infrastructure\ProcessManager;

use Inowas\Common\Id\ModflowId;
use Inowas\ModflowModel\Model\Command\ModflowModel\DeleteModflowModel;
use Inowas\ScenarioAnalysis\Model\Event\ScenarioAnalysisWasDeleted;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisAggregate;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisList;
use Prooph\ServiceBus\CommandBus;

final class DeleteScenarioAnalysisProcessManager
{

    /** @var ScenarioAnalysisList $scenarioAnalysisList */
    private $list;

    /** @var  CommandBus */
    private $commandBus;

    public function __construct(CommandBus $commandBus, ScenarioAnalysisList $list) {
        $this->commandBus = $commandBus;
        $this->list = $list;
    }

    public function onScenarioAnalysisWasDeleted(ScenarioAnalysisWasDeleted $event): void
    {

        /** @var ScenarioAnalysisAggregate $scenarioAnalysis */
        $scenarioAnalysis = $this->list->get($event->scenarioAnalysisId());

        $basemodelId = $scenarioAnalysis->baseModelId();
        $this->commandBus->dispatch(DeleteModflowModel::byIdAndUser($basemodelId, $event->userId()));

        $scenarioIds = $scenarioAnalysis->scenarios();

        foreach ($scenarioIds as $scenarioId){
            $this->commandBus->dispatch(DeleteModflowModel::byIdAndUser(ModflowId::fromString($scenarioId), $event->userId()));
        }
    }
}
