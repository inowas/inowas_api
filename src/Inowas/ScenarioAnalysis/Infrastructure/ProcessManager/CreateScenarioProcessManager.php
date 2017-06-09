<?php

declare(strict_types=1);

namespace Inowas\ScenarioAnalysis\Infrastructure\ProcessManager;

use Inowas\Common\Id\ModflowId;
use Inowas\ModflowModel\Infrastructure\Projection\ModelList\ModelFinder;
use Inowas\ModflowModel\Model\Command\CloneModflowModel;
use Inowas\ScenarioAnalysis\Model\Event\ScenarioWasCreated;
use Prooph\ServiceBus\CommandBus;

final class CreateScenarioProcessManager
{

    /** @var  ModelFinder */
    private $modelFinder;

    /** @var  CommandBus */
    private $commandBus;

    public function __construct(CommandBus $commandBus, ModelFinder $modelFinder) {
        $this->commandBus = $commandBus;
        $this->modelFinder = $modelFinder;
    }

    public function onScenarioWasCreated(ScenarioWasCreated $event): void
    {
        $existingSoilmodelId = $this->modelFinder->getSoilmodelIdByModelId($event->baseModelId());
        $newCalculationId = ModflowId::generate();

        $this->commandBus->dispatch(CloneModflowModel::byIdWithExistingSoilmodel(
            $event->baseModelId(),
            $event->userId(),
            $event->scenarioId(),
            $existingSoilmodelId,
            $newCalculationId
        ));
    }
}
