<?php

declare(strict_types=1);

namespace Inowas\ScenarioAnalysis\Infrastructure\ProcessManager;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Soilmodel\SoilmodelId;
use Inowas\ModflowModel\Model\Command\CloneModflowModel;
use Inowas\ScenarioAnalysis\Model\Event\ScenarioAnalysisWasCloned;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisAggregate;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisList;
use Prooph\ServiceBus\CommandBus;

final class CloneScenarioAnalysisProcessManager
{

    /** @var ScenarioAnalysisList $scenarioAnalysisList */
    private $list;

    /** @var  CommandBus */
    private $commandBus;

    public function __construct(CommandBus $commandBus, ScenarioAnalysisList $list) {
        $this->commandBus = $commandBus;
        $this->list = $list;
    }

    public function onScenarioAnalysisWasCloned(ScenarioAnalysisWasCloned $event): void
    {
        // GET ORIGINAL SCENARIOANALYSIS
        /** @var ScenarioAnalysisAggregate $originalScenarioAnalysis */
        $originalScenarioAnalysis = $this->list->get($event->fromScenarioAnalysisId());
        $basemodelId = $originalScenarioAnalysis->baseModelId();

        // -> CLONE BASEMODEL WITH NEW BASEMODEL-ID AND USER AND SOILMODEL
        $newModelId = $event->baseModelId();
        $newSoilModelId = SoilmodelId::generate();
        $newCalculationId = ModflowId::generate();
        $userId = $event->userId();
        $this->commandBus->dispatch(CloneModflowModel::byIdAndCloneSoilmodel($basemodelId, $userId, $newModelId, $newSoilModelId, $newCalculationId));

        // -> CLONE SCENARIOS WITH NEW IDS AND USER WITHOUT SOILMODEL
        $newScenarioIds = $event->scenarios();
        foreach ($originalScenarioAnalysis->scenarios() as $key => $scenario){
            $scenarioId = ModflowId::fromString($scenario);
            $newScenarioId = ModflowId::fromString($newScenarioIds[$key]);
            $newCalculationId = ModflowId::generate();
            $this->commandBus->dispatch(CloneModflowModel::byIdWithExistingSoilmodel($scenarioId, $userId, $newScenarioId, $newSoilModelId, $newCalculationId));
        }
    }
}
