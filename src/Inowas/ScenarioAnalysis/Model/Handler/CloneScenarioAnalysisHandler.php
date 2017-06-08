<?php

declare(strict_types=1);

namespace Inowas\ScenarioAnalysis\Model\Handler;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\ModflowModel\Model\Command\CloneModflowModel;
use Inowas\ScenarioAnalysis\Model\Command\CloneScenarioAnalysis;
use Inowas\ScenarioAnalysis\Model\Exception\ScenarioAnalysisNotFoundException;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisAggregate;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisId;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisList;
use Prooph\ServiceBus\CommandBus;

final class CloneScenarioAnalysisHandler
{

    private $commandBus;
    private $scenarioAnalysisList;

    public function __construct(ScenarioAnalysisList $scenarioAnalysisList, CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
        $this->scenarioAnalysisList = $scenarioAnalysisList;
    }

    public function __invoke(CloneScenarioAnalysis $command)
    {
        /** @var ScenarioAnalysisAggregate $scenarioAnalysis */
        $scenarioAnalysis = $this->scenarioAnalysisList->get($command->scenarioAnalysisId());

        if (! $scenarioAnalysis) {
            throw ScenarioAnalysisNotFoundException::withId($command->scenarioAnalysisId());
        }

        $baseModelId = $scenarioAnalysis->baseModelId();
        $clonedBaseModelId = ModflowId::generate();
        $userId = $command->userId();
        $this->cloneModel($clonedBaseModelId, $userId, $baseModelId);

        $scenarios = $scenarioAnalysis->scenarios();
        $clonedScenarios = array();
        foreach ($scenarios as $scenario){
            $originalId = ModflowId::fromString($scenario);
            $cloneId = ModflowId::generate();
            $this->cloneModel($cloneId, $userId, $originalId);
            $clonedScenarios[] = $cloneId->toString();
        }

        $scenarioAnalysis = ScenarioAnalysisAggregate::cloneWithIdUserIdAndAggregate(
            $command->scenarioAnalysisId(),
            ScenarioAnalysisId::generate(),
            $userId,
            $clonedBaseModelId,
            $clonedScenarios,
            $scenarioAnalysis
        );

        $this->scenarioAnalysisList->add($scenarioAnalysis);
    }

    private function cloneModel(ModflowId $cloneId, UserId $userId, ModflowId $originalId): void
    {
        $this->commandBus->dispatch(CloneModflowModel::fromBaseModel($originalId, $userId, $cloneId));
        #$originalModel = $this->modelList->get($originalId);
        #$clonedModel = ModflowModelAggregate::cloneWithIdUserAndAggregate($cloneId, $userId, $originalModel);
        #$this->modelList->add($clonedModel);
    }
}
