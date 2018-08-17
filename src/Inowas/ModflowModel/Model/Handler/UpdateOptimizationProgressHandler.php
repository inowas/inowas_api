<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Handler;

use Inowas\ModflowModel\Model\AMQP\ModflowOptimizationResponse;
use Inowas\ModflowModel\Model\Command\UpdateOptimizationProgress;
use Inowas\ModflowModel\Model\Exception\ModflowModelNotFoundException;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;

final class UpdateOptimizationProgressHandler
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
     * @param UpdateOptimizationProgress $command
     * @throws \Exception
     */
    public function __invoke(UpdateOptimizationProgress $command)
    {
        /** @var ModflowOptimizationResponse $response */
        $response = $command->response();

        /** @var ModflowModelAggregate $model */
        $model = $this->modelList->get($response->modelId());

        if (!$model) {
            throw ModflowModelNotFoundException::withModelId($response->modelId());
        }

        $model->updateOptimizationProgress($response->optimizationId(), $response->progress(), $response->solutions());
        $this->modelList->save($model);
    }
}
