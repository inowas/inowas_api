<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Handler;

use Inowas\Common\Modflow\OptimizationState;
use Inowas\ModflowModel\Model\Command\CancelOptimization;
use Inowas\ModflowModel\Model\Exception\ModflowModelNotFoundException;
use Inowas\ModflowModel\Model\Exception\WriteAccessFailedException;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;

final class CancelOptimizationHandler
{

    /** @var  ModflowModelList */
    private $modelList;

    /**
     * @param ModflowModelList $modelList
     */
    public function __construct(ModflowModelList $modelList)
    {
        $this->modelList = $modelList;
    }

    /**
     * @param CancelOptimization $command
     * @throws \Exception
     */
    public function __invoke(CancelOptimization $command)
    {
        /** @var ModflowModelAggregate $modflowModel */
        $modflowModel = $this->modelList->get($command->modflowModelId());

        if (!$modflowModel) {
            throw ModflowModelNotFoundException::withModelId($command->modflowModelId());
        }

        if (!$modflowModel->userId()->sameValueAs($command->userId())) {
            throw WriteAccessFailedException::withUserAndOwner($command->userId(), $modflowModel->userId());
        }

        $modflowModel->updateOptimizationCalculationStateByUser($command->userId(), $command->optimizationId(), OptimizationState::cancelling());
        $this->modelList->save($modflowModel);
    }
}
