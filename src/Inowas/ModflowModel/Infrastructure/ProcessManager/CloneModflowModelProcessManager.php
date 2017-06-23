<?php

namespace Inowas\ModflowModel\Infrastructure\ProcessManager;

use Inowas\ModflowModel\Model\Event\ModflowModelWasCloned;
use Inowas\ModflowModel\Model\ModflowModelAggregate;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\Soilmodel\Model\Command\CloneSoilmodel;
use Prooph\ServiceBus\CommandBus;

class CloneModflowModelProcessManager
{

    /** @var  CommandBus */
    private $commandBus;

    /** @var  ModflowModelList */
    private $modelList;

    public function __construct(CommandBus $commandBus, ModflowModelList $modelList)
    {
        $this->commandBus = $commandBus;
        $this->modelList = $modelList;
    }

    public function onModflowModelWasCloned(ModflowModelWasCloned $event): void
    {
        if ($event->cloneSoilmodel()) {
            /** @var ModflowModelAggregate $model */
            $model = $this->modelList->get($event->baseModelId());
            $oldSoilModelId = $model->soilmodelId();
            $this->commandBus->dispatch(CloneSoilmodel::byUserWithModelId($event->soilmodelId(), $event->userId(), $oldSoilModelId));
        }
    }
}
