<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Handler;

use Inowas\ModflowModel\Infrastructure\Projection\Optimization\OptimizationProjector;
use Inowas\ModflowModel\Model\Command\UpdateOptimizationInput;
use Inowas\ModflowModel\Model\Event\OptimizationInputWasUpdated;
use Inowas\ModflowModel\Model\Exception\ModflowModelNotFoundException;
use Inowas\ModflowModel\Model\Exception\WriteAccessFailedException;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;

final class UpdateOptimizationInputHandler
{

    /** @var  ModflowModelList */
    private $modelList;

    /** @var  OptimizationProjector */
    private $projector;

    /**
     * @param ModflowModelList $modelList
     * @param OptimizationProjector $projector
     */
    public function __construct(ModflowModelList $modelList, OptimizationProjector $projector)
    {
        $this->modelList = $modelList;
        $this->projector = $projector;
    }

    /**
     * @param UpdateOptimizationInput $command
     * @throws \Exception
     */
    public function __invoke(UpdateOptimizationInput $command)
    {

        /** @var ModflowModelAggregate $modflowModel */
        $modflowModel = $this->modelList->get($command->modflowModelId());

        if (!$modflowModel) {
            throw ModflowModelNotFoundException::withModelId($command->modflowModelId());
        }

        if (!$modflowModel->userId()->sameValueAs($command->userId())) {
            throw WriteAccessFailedException::withUserAndOwner($command->userId(), $modflowModel->userId());
        }

        $this->projector->onOptimizationInputWasUpdated(
            OptimizationInputWasUpdated::byUserToModel(
                $command->userId(), $command->modflowModelId(), $command->input()
            )
        );
    }
}
