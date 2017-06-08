<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Handler;

use Inowas\Common\Soilmodel\SoilmodelId;
use Inowas\ModflowModel\Model\Command\CloneModflowModel;
use Inowas\ModflowModel\Model\Exception\ModflowModelNotFoundException;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;
use Inowas\Soilmodel\Model\Command\CloneSoilmodel;
use Prooph\ServiceBus\CommandBus;

final class CloneModflowModelHandler
{

    /** @var  CommandBus */
    private $commandBus;

    /** @var  ModflowModelList */
    private $modelList;

    public function __construct(ModflowModelList $modelList, CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
        $this->modelList = $modelList;
    }

    public function __invoke(CloneModflowModel $command)
    {
        /** @var ModflowModelAggregate $modflowModel */
        $modflowModel = $this->modelList->get($command->baseModelId());

        if (!$modflowModel){
            throw ModflowModelNotFoundException::withModelId($command->baseModelId());
        }

        $newSoilModelId = SoilmodelId::generate();
        $this->commandBus->dispatch(CloneSoilmodel::byUserWithModelId($newSoilModelId, $command->userId(), $modflowModel->soilmodelId()));

        /** @var ModflowModelAggregate $modflowModel */
        $newModel = ModflowModelAggregate::cloneWithIdUserSoilmodelIdAndAggregate($command->newModelId(), $command->userId(), $newSoilModelId, $modflowModel);
        $this->modelList->add($newModel);
    }
}
