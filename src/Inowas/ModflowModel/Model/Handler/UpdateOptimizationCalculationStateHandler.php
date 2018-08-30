<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Handler;

use Inowas\ModflowModel\Model\Command\UpdateOptimizationCalculationState;
use Inowas\ModflowModel\Model\Exception\ModflowModelNotFoundException;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;

final class UpdateOptimizationCalculationStateHandler
{
    /** @var  ModflowModelList */
    private $modelList;

    public function __construct(ModflowModelList $modelList)
    {
        $this->modelList = $modelList;
    }

    public function __invoke(UpdateOptimizationCalculationState $command)
    {
        /** @var ModflowModelAggregate $modflowModel */
        $modflowModel = $this->modelList->get($command->modelId());

        if (!$modflowModel instanceof ModflowModelAggregate) {
            throw ModflowModelNotFoundException::withModelId($command->modelId());
        }

        $modflowModel->updateOptimizationCalculationState($command->optimizationId(), $command->state());
        $this->modelList->save($modflowModel);
    }
}
