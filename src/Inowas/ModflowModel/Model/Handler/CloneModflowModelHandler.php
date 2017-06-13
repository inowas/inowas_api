<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Handler;

use Inowas\ModflowCalculation\Infrastructure\Projection\Calculation\CalculationListFinder;
use Inowas\ModflowCalculation\Model\Command\CalculateModflowModelCalculation;
use Inowas\ModflowCalculation\Model\Command\CloneModflowModelCalculation;
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

    public function __construct(ModflowModelList $modelList, CalculationListFinder $calculationFinder, CommandBus $commandBus)
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

        // Let's clone the modflowCalculation first with the new calculationId
        $oldCalculationId = $modflowModel->calculationId();
        $this->commandBus->dispatch(CloneModflowModelCalculation::byUserWithModelId($command->userId(), $oldCalculationId, $command->newCalculationId(), $command->baseModelId(), $command->newModelId()));
        $this->commandBus->dispatch(CalculateModflowModelCalculation::byUserWithCalculationId($command->userId(), $command->newCalculationId()));

        // Clone the soilmodel and model if set
        if ($command->cloneSoilmodel()) {
            $this->commandBus->dispatch(CloneSoilmodel::byUserWithModelId($command->soilmodelId(), $command->userId(), $modflowModel->soilmodelId()));
        }

        /** @var ModflowModelAggregate $modflowModel */
        $newModel = ModflowModelAggregate::cloneWithIdUserSoilmodelCalculationIdAndAggregate(
            $command->newModelId(),
            $command->userId(),
            $command->soilmodelId(),
            $command->newCalculationId(),
            $modflowModel
        );
        $this->modelList->add($newModel);
    }
}
