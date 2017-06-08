<?php

declare(strict_types=1);

namespace Inowas\ModflowCalculation\Model\Handler;

use Inowas\Common\Modflow\StressPeriods;
use Inowas\ModflowCalculation\Model\Command\CreateModflowModelCalculation;
use Inowas\ModflowCalculation\Model\ModflowCalculationAggregate;
use Inowas\ModflowCalculation\Model\ModflowCalculationList;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;
use Inowas\ModflowModel\Service\ModflowModelManager;

final class CreateModflowModelCalculationHandler
{

    /** @var  ModflowModelList */
    private $modflowModelList;

    /** @var  ModflowCalculationList */
    private $modelCalculationList;

    /** @var  ModflowModelManager */
    private $modflowModelManager;

    public function __construct(
        ModflowModelList $modflowModelList,
        ModflowCalculationList $modelCalculationList,
        ModflowModelManager $modflowModelManager
    ) {
        $this->modelCalculationList = $modelCalculationList;
        $this->modflowModelList = $modflowModelList;
        $this->modflowModelManager = $modflowModelManager;
    }

    public function __invoke(CreateModflowModelCalculation $command)
    {
        /** @var ModflowModelAggregate $modflowModel */
        $modflowModel = $this->modflowModelList->get($command->modflowModelId());

        /** @var StressPeriods $stressPeriods */
        $stressPeriods = $this->modflowModelManager->calculateStressPeriods($modflowModel->modflowModelId(), $command->startDateTime(), $command->endDateTime(), $modflowModel->timeUnit());

        $calculation = ModflowCalculationAggregate::create(
            $command->calculationId(),
            $modflowModel->modflowModelId(),
            $modflowModel->ownerId(),
            $command->startDateTime(),
            $command->endDateTime(),
            $modflowModel->lengthUnit(),
            $modflowModel->timeUnit(),
            $stressPeriods
        );

        $this->modelCalculationList->add($calculation);
    }
}
