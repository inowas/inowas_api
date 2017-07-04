<?php

namespace Inowas\ModflowModel\Infrastructure\ProcessManager;

use Inowas\Common\Id\BoundaryId;
use Inowas\ModflowBoundary\Model\Command\CloneBoundary;
use Inowas\ModflowModel\Model\Event\ModflowModelWasCloned;
use Inowas\ModflowModel\Model\ModflowModelAggregate;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowBoundary\Service\BoundaryManager;
use Inowas\Soilmodel\Model\Command\CloneSoilmodel;
use Prooph\ServiceBus\CommandBus;

class CloneModflowModelProcessManager
{

    /** @var  CommandBus */
    private $commandBus;

    /** @var  ModflowModelList */
    private $modelList;

    /** @var  \Inowas\ModflowBoundary\Service\BoundaryManager */
    private $boundaryManager;

    public function __construct(CommandBus $commandBus, ModflowModelList $modelList, BoundaryManager $boundaryManager)
    {
        $this->commandBus = $commandBus;
        $this->modelList = $modelList;
        $this->boundaryManager = $boundaryManager;
    }

    public function onModflowModelWasCloned(ModflowModelWasCloned $event): void
    {
        if ($event->cloneSoilmodel()) {
            /** @var ModflowModelAggregate $model */
            $model = $this->modelList->get($event->baseModelId());
            $oldSoilModelId = $model->soilmodelId();
            $this->commandBus->dispatch(CloneSoilmodel::byUserWithModelId($event->soilmodelId(), $event->userId(), $oldSoilModelId));
        }

        $boundaryIds = $this->boundaryManager->getBoundaryIds($event->baseModelId());

        foreach ($boundaryIds as $boundaryId) {
            $this->commandBus->dispatch(CloneBoundary::withIds($boundaryId, BoundaryId::generate(), $event->modelId()));
        }
    }
}
