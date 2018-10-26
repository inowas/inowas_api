<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Handler;

use Inowas\Common\Id\CalculationId;
use Inowas\Common\Modflow\OptimizationState;
use Inowas\ModflowModel\Infrastructure\Projection\ModelList\ModelFinder;
use Inowas\ModflowModel\Infrastructure\Projection\Optimization\OptimizationFinder;
use Inowas\ModflowModel\Infrastructure\Projection\Optimization\OptimizationProjector;
use Inowas\ModflowModel\Model\AMQP\ModflowOptimizationStartRequest;
use Inowas\ModflowModel\Model\Command\CalculateOptimization;
use Inowas\ModflowModel\Model\Event\OptimizationStateWasUpdated;
use Inowas\ModflowModel\Model\Exception\ModflowModelDirtyException;
use Inowas\ModflowModel\Model\Exception\ModflowModelNotFoundException;
use Inowas\ModflowModel\Model\Exception\ModflowModelOptimizationFailedException;
use Inowas\ModflowModel\Model\Exception\WriteAccessFailedException;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;
use Inowas\ModflowModel\Service\AMQPBasicProducer;
use Inowas\ModflowModel\Service\ModflowPackagesManager;

final class CalculateOptimizationHandler
{
    /** @var ModelFinder */
    private $modelFinder;

    /** @var  ModflowModelList */
    private $modelList;

    /** @var OptimizationFinder */
    private $optimizationFinder;

    /** @var ModflowPackagesManager */
    private $packagesManager;

    /** @var  AMQPBasicProducer */
    private $producer;

    /** @var  OptimizationProjector */
    private $projector;

    /**
     * @param ModflowModelList $modelList
     * @param AMQPBasicProducer $producer
     * @param ModelFinder $modelFinder
     * @param OptimizationFinder $optimizationFinder
     * @param ModflowPackagesManager $packagesManager
     * @param OptimizationProjector $projector
     */
    public function __construct(
        ModflowModelList $modelList,
        AMQPBasicProducer $producer,
        ModelFinder $modelFinder,
        OptimizationFinder $optimizationFinder,
        ModflowPackagesManager $packagesManager,
        OptimizationProjector $projector
    )
    {
        $this->modelFinder = $modelFinder;
        $this->modelList = $modelList;
        $this->producer = $producer;
        $this->optimizationFinder = $optimizationFinder;
        $this->packagesManager = $packagesManager;
        $this->projector = $projector;
    }

    /**
     * @param CalculateOptimization $command
     * @throws \Exception
     */
    public function __invoke(CalculateOptimization $command)
    {
        /** @var ModflowModelAggregate $modflowModel */
        $modflowModel = $this->modelList->get($command->modflowModelId());

        if (!$modflowModel) {
            throw ModflowModelNotFoundException::withModelId($command->modflowModelId());
        }

        if (!$modflowModel->userId()->sameValueAs($command->userId())) {
            throw WriteAccessFailedException::withUserAndOwner($command->userId(), $modflowModel->userId());
        }

        if ($this->modelFinder->isDirty($command->modflowModelId())) {
            throw ModflowModelDirtyException::withModelId($command->modflowModelId());
        }

        $calculationId = $this->modelFinder->getCalculationIdByModelId($command->modflowModelId());

        if (!$calculationId instanceof CalculationId) {
            throw ModflowModelOptimizationFailedException::withModelId(
                $command->modflowModelId(),
                $command->optimizationId()
            );
        }

        $command->isInitial() ? $state = OptimizationState::new() : $state = OptimizationState::started();
        $this->projector->onOptimizationStateWasUpdated(
            OptimizationStateWasUpdated::withUserIdModelIdAndState(
                $command->userId(),
                $command->modflowModelId(),
                $command->optimizationId(),
                $state
            )
        );

        try {
            $this->producer->publish(ModflowOptimizationStartRequest::startOptimization(
                $command->modflowModelId(),
                $this->packagesManager->getPackages($calculationId),
                $this->optimizationFinder->getOptimizationByModelId($command->modflowModelId())->input()
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
                OptimizationState::calculating()
            )
        );
    }
}
