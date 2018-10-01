<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Handler;

use Inowas\Common\Modflow\OptimizationState;
use Inowas\ModflowModel\Infrastructure\Projection\Optimization\OptimizationProjector;
use Inowas\ModflowModel\Model\AMQP\ModflowOptimizationStopRequest;
use Inowas\ModflowModel\Model\Command\CancelOptimization;
use Inowas\ModflowModel\Model\Event\OptimizationStateWasUpdated;
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

    /** @var OptimizationProjector $projector */
    private $projector;

    /**
     * @param ModflowModelList $modelList
     * @param AMQPBasicProducer $producer
     * @param OptimizationProjector $projector
     */
    public function __construct(ModflowModelList $modelList, AMQPBasicProducer $producer, OptimizationProjector $projector)
    {
        $this->modelList = $modelList;
        $this->producer = $producer;
        $this->projector = $projector;
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

        try {
            $this->producer->publish(ModflowOptimizationStopRequest::stopOptimization(
                $command->modflowModelId(),
                $command->optimizationId()
            ));
        } catch (\Exception $e) {
            $this->projector->onOptimizationStateWasUpdated(
                OptimizationStateWasUpdated::withUserIdModelIdAndState(
                    $command->userId(),
                    $command->modflowModelId(),
                    $command->optimizationId(),
                    OptimizationState::errorPublishing()
                )
            );

            return;
        }

        $this->projector->onOptimizationStateWasUpdated(
            OptimizationStateWasUpdated::withUserIdModelIdAndState(
                $command->userId(),
                $command->modflowModelId(),
                $command->optimizationId(),
                OptimizationState::cancelled()
            )
        );
    }
}
