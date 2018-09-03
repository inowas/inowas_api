<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Handler;

use Inowas\Common\Modflow\OptimizationState;
use Inowas\ModflowModel\Model\AMQP\ModflowOptimizationStopRequest;
use Inowas\ModflowModel\Model\Command\CancelOptimization;
use Inowas\ModflowModel\Model\Exception\ModflowModelNotFoundException;
use Inowas\ModflowModel\Model\Exception\WriteAccessFailedException;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;
use Inowas\ModflowModel\Service\AMQPBasicProducer;

final class CancelOptimizationHandler
{

    /** @var  ModflowModelList */
    private $modelList;

    /** @var AMQPBasicProducer $producer */
    private $producer;

    /**
     * @param ModflowModelList $modelList
     * @param AMQPBasicProducer $producer
     */
    public function __construct(ModflowModelList $modelList, AMQPBasicProducer $producer)
    {
        $this->modelList = $modelList;
        $this->producer = $producer;
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

        try {
            $this->producer->publish(ModflowOptimizationStopRequest::stopOptimization(
                $command->modflowModelId(),
                $command->optimizationId()
            ));
        } catch (\Exception $e) {
            $modflowModel->updateOptimizationCalculationStateByUser($command->userId(), $command->optimizationId(), OptimizationState::errorPublishing());
        }

        $modflowModel->updateOptimizationCalculationState($command->optimizationId(), OptimizationState::cancelled());
        $this->modelList->save($modflowModel);
    }
}
