<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Handler;

use Inowas\ModflowModel\Model\AMQP\FlopyCalculationRequest;
use Inowas\ModflowModel\Model\Command\CalculateModflowModel;
use Inowas\ModflowModel\Model\Exception\ModflowModelNotFoundException;
use Inowas\ModflowModel\Model\Exception\WriteAccessFailedException;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;
use Inowas\ModflowModel\Service\AMQPFlopyCalculation;
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
     * @param AMQPFlopyCalculation $calculator
     */
    public function __construct(
        ModflowModelList $modelList,
        ModflowPackagesManager $packagesManager,
        AMQPFlopyCalculation $calculator
    )
    {
        $this->calculator = $calculator;
        $this->packagesManager = $packagesManager;
        $this->modelList = $modelList;
    }

    public function __invoke(CalculateModflowModel $command)
    {
        /** @var ModflowModelAggregate $modflowModel */
        $modflowModel = $this->modelList->get($command->modelId());

        if (!$modflowModel){
            throw ModflowModelNotFoundException::withModelId($command->modelId());
        }

        if (! $command->fromTerminal() && ! $modflowModel->userId()->sameValueAs($command->userId())){
            throw WriteAccessFailedException::withUserAndOwner($command->userId(), $modflowModel->userId());
        }

        $calculationId = $this->packagesManager->recalculate($modflowModel->modflowModelId());

        $modflowModel->preprocessingWasFinished($calculationId);
        $packages = $this->packagesManager->getPackages($calculationId);
        $request = FlopyCalculationRequest::fromParams($command->modelId(), $calculationId, $packages);
        $this->calculator->calculate($request);
        $modflowModel->calculationWasStarted($calculationId);

        $this->modelList->save($modflowModel);
    }
}
