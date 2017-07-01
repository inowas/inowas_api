<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Handler\ModflowModel;

use Inowas\Common\Soilmodel\SoilmodelId;
use Inowas\ModflowModel\Model\Command\ModflowModel\CloneModflowModel;
use Inowas\ModflowModel\Model\Exception\ModflowModelNotFoundException;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;

final class CloneModflowModelHandler
{
    /** @var  ModflowModelList */
    private $modelList;

    public function __construct(ModflowModelList $modelList)
    {
        $this->modelList = $modelList;
    }

    public function __invoke(CloneModflowModel $command)
    {
        /** @var ModflowModelAggregate $modflowModel */
        $modflowModel = $this->modelList->get($command->baseModelId());

        if (!$modflowModel){
            throw ModflowModelNotFoundException::withModelId($command->baseModelId());
        }

        $soilmodelId = $modflowModel->soilmodelId();
        if ($command->cloneSoilmodel()){
            $soilmodelId = SoilmodelId::generate();
        }

        /** @var ModflowModelAggregate $modflowModel */
        $newModel = ModflowModelAggregate::clone(
            $command->newModelId(),
            $command->userId(),
            $soilmodelId,
            $modflowModel
        );

        $this->modelList->add($newModel);
    }
}
