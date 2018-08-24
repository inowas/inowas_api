<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Handler;

use Inowas\Common\Id\ModflowId;
use Inowas\ModflowModel\Infrastructure\Projection\Optimization\OptimizationFinder;
use Inowas\ModflowModel\Model\AMQP\ModflowOptimizationResponse;
use Inowas\ModflowModel\Model\Command\UpdateOptimizationProgress;
use Inowas\ModflowModel\Model\Exception\ModflowModelNotFoundException;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;

final class UpdateOptimizationProgressHandler
{

    /** @var  ModflowModelList */
    private $modelList;

    /** @var OptimizationFinder */
    private $optimizationFinder;

    /**
     * @param ModflowModelList $modelList
     * @param OptimizationFinder $optimizationFinder
     */
    public function __construct(ModflowModelList $modelList, OptimizationFinder $optimizationFinder)
    {
        $this->modelList = $modelList;
        $this->optimizationFinder = $optimizationFinder;
    }

    /**
     * @param UpdateOptimizationProgress $command
     * @throws \Exception
     */
    public function __invoke(UpdateOptimizationProgress $command)
    {
        /** @var ModflowOptimizationResponse $response */
        $response = $command->response();

        /** @var ModflowId $modelId */
        $modelId = $this->optimizationFinder->getModelId($command->optimizationId());

        /** @var ModflowModelAggregate $model */
        $model = $this->modelList->get($modelId);

        if (!$model) {
            throw ModflowModelNotFoundException::withModelId($modelId);
        }

        $model->updateOptimizationProgress($response->optimizationId(), $response->progress(), $response->solutions());
        $this->modelList->save($model);
    }
}
