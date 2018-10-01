<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Handler;

use Inowas\ModflowModel\Infrastructure\Projection\Optimization\OptimizationProjector;
use Inowas\ModflowModel\Model\AMQP\ModflowOptimizationResponse;
use Inowas\ModflowModel\Model\Command\UpdateOptimizationCalculationState;
use Inowas\ModflowModel\Model\Event\OptimizationStateWasUpdated;
use Inowas\ModflowModel\Model\Exception\ModflowModelNotFoundException;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;

final class UpdateOptimizationCalculationStateHandler
{
    /** @var  ModflowModelList */
    private $modelList;

    /** @var  OptimizationProjector */
    private $projector;

    public function __construct(ModflowModelList $modelList, OptimizationProjector $projector)
    {
        $this->modelList = $modelList;
        $this->projector = $projector;

    }

    public function __invoke(UpdateOptimizationCalculationState $command)
    {
        /** @var ModflowModelAggregate $modflowModel */
        $modflowModel = $this->modelList->get($command->modelId());

        if (!$modflowModel instanceof ModflowModelAggregate) {
            throw ModflowModelNotFoundException::withModelId($command->modelId());
        }

        $response = $command->response();

        if ($response instanceof ModflowOptimizationResponse) {
            $this->projector->onOptimizationStateWasUpdated(
                OptimizationStateWasUpdated::withModelIdStateAndResponse(
                    $command->modelId(),
                    $command->optimizationId(),
                    $command->state(),
                    $command->response()
                )
            );
            return;
        }

        $this->projector->onOptimizationStateWasUpdated(
            OptimizationStateWasUpdated::withModelIdAndState(
                $command->modelId(),
                $command->optimizationId(),
                $command->state()
            )
        );
    }
}
