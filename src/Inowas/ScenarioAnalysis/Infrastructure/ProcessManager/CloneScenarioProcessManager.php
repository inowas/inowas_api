<?php

declare(strict_types=1);

namespace Inowas\ScenarioAnalysis\Infrastructure\ProcessManager;

use Inowas\Common\Id\ModflowId;
use Inowas\ModflowModel\Model\Command\CloneModflowModel;
use Inowas\ModflowModel\Model\ModflowModelAggregate;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ScenarioAnalysis\Model\Event\ScenarioWasCloned;
use Prooph\ServiceBus\CommandBus;

final class CloneScenarioProcessManager
{

    /** @var ModflowModelList */
    private $list;

    /** @var  CommandBus */
    private $commandBus;

    public function __construct(CommandBus $commandBus, ModflowModelList $list) {
        $this->commandBus = $commandBus;
        $this->list = $list;
    }

    public function onScenarioWasCloned(ScenarioWasCloned $event): void
    {
        // GET ORIGINAL SCENARIOANALYSIS
        /** @var ModflowModelAggregate $model */
        $model = $this->list->get($event->baseModelId());

        $basemodelId = $event->baseModelId();
        $userId = $event->userId();
        $newModelId = $event->scenarioId();
        $soilmodelId = $model->soilmodelId();
        $newCalculationId = ModflowId::generate();

        $this->commandBus->dispatch(CloneModflowModel::byIdWithExistingSoilmodel($basemodelId, $userId, $newModelId, $soilmodelId, $newCalculationId));
    }
}
