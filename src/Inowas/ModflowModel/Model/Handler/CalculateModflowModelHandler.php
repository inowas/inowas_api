<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Handler;

use Inowas\ModflowModel\Model\AMQP\CalculationRequest;
use Inowas\ModflowModel\Model\Command\CalculateModflowModel;
use Inowas\ModflowModel\Model\Exception\ModflowModelNotFoundException;
use Inowas\ModflowModel\Model\Exception\WriteAccessFailedException;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;
use Inowas\ModflowModel\Service\AMQPModflowCalculation;
use Inowas\ModflowModel\Service\ModflowPackagesManager;

final class CalculateModflowModelHandler
{

    /** @var  AMQPModflowCalculation */
    private $calculator;

    /** @var  ModflowModelList */
    private $modelList;

    /** @var  ModflowPackagesManager */
    private $packagesManager;

    /**
     * ChangeModflowModelBoundingBoxHandler constructor.
     * @param ModflowModelList $modelList
     * @param ModflowPackagesManager $packagesManager
     * @param AMQPModflowCalculation $calculator
     */
    public function __construct(
        ModflowModelList $modelList,
        ModflowPackagesManager $packagesManager,
        AMQPModflowCalculation $calculator
    )
    {
        $this->calculator = $calculator;
        $this->packagesManager = $packagesManager;
        $this->modelList = $modelList;
    }

    public function __invoke(CalculateModflowModel $command)
    {
        /** @var ModflowModelAggregate $modflowModel */
        $modflowModel = $this->modelList->get($command->modflowModelId());

        if (!$modflowModel){
            throw ModflowModelNotFoundException::withModelId($command->modflowModelId());
        }

        if (! $modflowModel->userId()->sameValueAs($command->userId())){
            throw WriteAccessFailedException::withUserAndOwner($command->userId(), $modflowModel->userId());
        }

        $calculationId = $this->packagesManager->recalculateModelAndSave($modflowModel->modflowModelId());
        $modflowModel->updateCalculationId($calculationId);
        $packages = $this->packagesManager->load($calculationId);

        $request = CalculationRequest::fromParams($command->modflowModelId(), $calculationId, $packages);
        $this->calculator->calculate($request);
        $modflowModel->calculationWasStarted($calculationId);
    }
}
