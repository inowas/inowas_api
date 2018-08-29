<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Handler;

use Inowas\ModflowModel\Model\Command\UpdateCalculationState;
use Inowas\ModflowModel\Model\Exception\ModflowModelNotFoundException;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;

final class UpdateCalculationStateHandler
{
    /** @var  ModflowModelList */
    private $modelList;

    public function __construct(ModflowModelList $modelList)
    {
        $this->modelList = $modelList;
    }

    public function __invoke(UpdateCalculationState $command)
    {
        /** @var ModflowModelAggregate $modflowModel */
        $modflowModel = $this->modelList->get($command->modelId());

        if (!$modflowModel instanceof ModflowModelAggregate) {
            throw ModflowModelNotFoundException::withModelId($command->modelId());
        }

        $modflowModel->updateCalculationState(null, $command->calculationId(), $command->state(), $command->response());
        $this->modelList->save($modflowModel);
    }
}
