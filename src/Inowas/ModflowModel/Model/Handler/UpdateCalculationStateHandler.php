<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Handler;

use Inowas\Common\Calculation\CalculationState;
use Inowas\ModflowModel\Model\Command\UpdateCalculationState;
use Inowas\ModflowModel\Model\Exception\ModflowModelNotFoundException;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;

final class UpdateCalculationStateHandler
{
    /** @var  ModflowModelList */
    private $modelList;

    public function __construct(ModflowModelList $modelList)
    {
        $this->modelList = $modelList;
    }

    public function __invoke(UpdateCalculationState $command)
    {
        /** @var ModflowModelAggregate $modflowModel */
        $modflowModel = $this->modelList->get($command->modelId());

        if (!$modflowModel instanceof ModflowModelAggregate) {
            throw ModflowModelNotFoundException::withModelId($command->modelId());
        }

        switch ($command->state()->toInt()) {
            case CalculationState::CALCULATION_PROCESS_STARTED:
                $modflowModel->startCalculationProcess($modflowModel->userId());
                break;
            case CalculationState::PREPROCESSING_FINISHED:
                $modflowModel->preprocessingWasFinished($command->calculationId());
                break;
            case CalculationState::CALCULATING:
                $modflowModel->calculationWasStarted($command->calculationId());
                break;
            case CalculationState::FINISHED:
                $modflowModel->calculationWasFinished($command->response());
                break;
        }

        $this->modelList->save($modflowModel);
    }
}
