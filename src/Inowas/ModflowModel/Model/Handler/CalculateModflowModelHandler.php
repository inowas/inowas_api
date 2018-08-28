<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Handler;

use Inowas\Common\Calculation\CalculationState;
use Inowas\ModflowModel\Model\Command\CalculateModflowModel;
use Inowas\ModflowModel\Model\Exception\ModflowModelNotFoundException;
use Inowas\ModflowModel\Model\Exception\WriteAccessFailedException;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;

final class CalculateModflowModelHandler
{
    /** @var  ModflowModelList */
    private $modelList;

    /**
     * ChangeModflowModelBoundingBoxHandler constructor.
     * @param ModflowModelList $modelList
     */
    public function __construct(ModflowModelList $modelList)
    {
        $this->modelList = $modelList;
    }

    /**
     * @param CalculateModflowModel $command
     * @throws \Inowas\ModflowModel\Model\Exception\WriteAccessFailedException
     * @throws ModflowModelNotFoundException
     * @throws \exception
     */
    public function __invoke(CalculateModflowModel $command)
    {
        /** @var ModflowModelAggregate $modflowModel */
        $modflowModel = $this->modelList->get($command->modelId());

        if (!$modflowModel) {
            throw ModflowModelNotFoundException::withModelId($command->modelId());
        }

        if (!$command->fromTerminal() && !$modflowModel->userId()->sameValueAs($command->userId())) {
            throw WriteAccessFailedException::withUserAndOwner($command->userId(), $modflowModel->userId());
        }

        $userId = $command->userId();
        if($command->fromTerminal()) {
            $userId = $modflowModel->userId();
        }

        $modflowModel->updateCalculationState($userId, null, CalculationState::calculationProcessStarted(), null);
        $this->modelList->save($modflowModel);
    }
}
