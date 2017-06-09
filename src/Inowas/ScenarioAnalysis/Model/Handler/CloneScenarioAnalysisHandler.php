<?php

declare(strict_types=1);

namespace Inowas\ScenarioAnalysis\Model\Handler;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Soilmodel\SoilmodelId;
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
        $soilmodelId = SoilmodelId::generate();
        $newCalculationId = ModflowId::generate();
        $userId = $command->userId();
        $this->cloneModelAndSoilmodel($clonedBaseModelId, $userId, $baseModelId, $soilmodelId, $newCalculationId);

        $scenarios = $scenarioAnalysis->scenarios();
        $clonedScenarios = array();
        foreach ($scenarios as $scenario){
            $originalId = ModflowId::fromString($scenario);
            $cloneId = ModflowId::generate();
            $newCalculationId = ModflowId::generate();
            $this->cloneModelWithoutSoilmodel($cloneId, $userId, $originalId, $soilmodelId, $newCalculationId);
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

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param ModflowId $cloneId
     * @param UserId $userId
     * @param ModflowId $originalId
     * @param SoilmodelId $newSoilmodelId
     * @param ModflowId $newCalculationId
     * @throws \Prooph\ServiceBus\Exception\CommandDispatchException
     */
    private function cloneModelAndSoilmodel(ModflowId $cloneId, UserId $userId, ModflowId $originalId, SoilmodelId $newSoilmodelId, ModflowId $newCalculationId): void
    {
        $this->commandBus->dispatch(CloneModflowModel::byIdAndCloneSoilmodel(
            $originalId,
            $userId,
            $cloneId,
            $newSoilmodelId,
            $newCalculationId
        ));
    }

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param ModflowId $cloneId
     * @param UserId $userId
     * @param ModflowId $originalId
     * @param SoilmodelId $existingSoilmodelId
     * @param ModflowId $newCalculationId
     * @throws \Prooph\ServiceBus\Exception\CommandDispatchException
     */
    private function cloneModelWithoutSoilmodel(ModflowId $cloneId, UserId $userId, ModflowId $originalId, SoilmodelId $existingSoilmodelId, ModflowId $newCalculationId): void
    {
        $this->commandBus->dispatch(CloneModflowModel::byIdWithExistingSoilmodel(
            $originalId, $userId, $cloneId, $existingSoilmodelId, $newCalculationId
        ));
    }
}
