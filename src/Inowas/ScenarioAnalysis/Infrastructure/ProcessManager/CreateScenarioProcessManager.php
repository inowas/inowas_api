<?php

declare(strict_types=1);

namespace Inowas\ScenarioAnalysis\Infrastructure\ProcessManager;

use Inowas\ModflowModel\Model\Command\ModflowModel\ChangeDescription;
use Inowas\ModflowModel\Model\Command\ModflowModel\ChangeName;
use Inowas\ModflowModel\Model\Command\ModflowModel\CloneModflowModel;
use Inowas\ScenarioAnalysis\Model\Event\ScenarioWasCreated;
use Prooph\ServiceBus\CommandBus;

final class CreateScenarioProcessManager
{
    /** @var  CommandBus */
    private $commandBus;

    public function __construct(CommandBus $commandBus) {
        $this->commandBus = $commandBus;
    }

    public function onScenarioWasCreated(ScenarioWasCreated $event): void
    {

        $this->commandBus->dispatch(CloneModflowModel::byIdWithoutSoilmodel(
            $event->baseModelId(),
            $event->userId(),
            $event->scenarioId()
        ));

        $this->commandBus->dispatch(ChangeName::forModflowModel($event->userId(), $event->scenarioId(), $event->name()));
        $this->commandBus->dispatch(ChangeDescription::forModflowModel($event->userId(), $event->scenarioId(), $event->description()));
    }
}
