<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Handler;

use Inowas\Common\Id\CalculationId;
use Inowas\Common\Modflow\OptimizationState;
use Inowas\ModflowModel\Infrastructure\Projection\ModelList\ModelFinder;
use Inowas\ModflowModel\Infrastructure\Projection\Optimization\OptimizationFinder;
use Inowas\ModflowModel\Model\AMQP\ModflowOptimizationStartRequest;
use Inowas\ModflowModel\Model\Command\CalculateOptimization;
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

    /**
     * @param ModflowModelList $modelList
     * @param AMQPBasicProducer $producer
     * @param ModelFinder $modelFinder
     * @param OptimizationFinder $optimizationFinder
     * @param ModflowPackagesManager $packagesManager
     */
    public function __construct(
        ModflowModelList $modelList,
        AMQPBasicProducer $producer,
        ModelFinder $modelFinder,
        OptimizationFinder $optimizationFinder,
        ModflowPackagesManager $packagesManager
    )
    {
        $this->modelFinder = $modelFinder;
        $this->modelList = $modelList;
        $this->producer = $producer;
        $this->optimizationFinder = $optimizationFinder;
        $this->packagesManager = $packagesManager;
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

        $modflowModel->updateOptimizationCalculationStateByUser($command->userId(), $command->optimizationId(), OptimizationState::started());

        try {
            $this->producer->publish(ModflowOptimizationStartRequest::startOptimization(
                $command->modflowModelId(),
                $this->packagesManager->getPackages($calculationId),
                $this->optimizationFinder->getOptimization($command->modflowModelId())->input()
            ));
        } catch (\Exception $e) {
            $modflowModel->updateOptimizationCalculationStateByUser($command->userId(), $command->optimizationId(), OptimizationState::errorPublishing());
        }

        $modflowModel->updateOptimizationCalculationStateByUser($command->userId(), $command->optimizationId(), OptimizationState::calculating());
        $this->modelList->save($modflowModel);
    }
}
