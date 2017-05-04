<?php

declare(strict_types=1);

namespace Inowas\ModflowCalculation\Model\Handler;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Modflow\StressPeriods;
use Inowas\ModflowCalculation\Model\Command\CreateModflowModelCalculation;
use Inowas\ModflowModel\Model\Exception\ModflowModelNotFoundException;
use Inowas\ModflowCalculation\Model\ModflowCalculationAggregate;
use Inowas\ModflowCalculation\Model\ModflowCalculationList;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;
use Inowas\ModflowModel\Service\ModflowModelManager;
use Inowas\Soilmodel\Model\SoilmodelList;

final class CreateModflowModelCalculationHandler
{

    /** @var  ModflowModelList */
    private $modelList;

    /** @var  ModflowModelList */
    private $modelCalculationList;

    /** @var  SoilmodelList */
    private $soilmodelList;

    /** @var  ModflowModelManager */
    private $modflowModelManager;

    public function __construct(
        ModflowModelList $modelList,
        SoilmodelList $soilmodelList,
        ModflowCalculationList $modelCalculationList,
        ModflowModelManager $modflowModelManager
    ) {
        $this->modelCalculationList = $modelCalculationList;
        $this->modelList = $modelList;
        $this->soilmodelList = $soilmodelList;
        $this->modflowModelManager = $modflowModelManager;
    }

    public function __invoke(CreateModflowModelCalculation $command)
    {
        /** @var ModflowModelAggregate $modflowModel */
        $modflowModel = $this->getModflowModel($command);

        /** @var StressPeriods $stressPeriods */
        $stressPeriods = $this->modflowModelManager->calculateStressPeriods($modflowModel->modflowModelId(), $command->startDateTime(), $command->endDateTime(), $modflowModel->timeUnit());

        $calculation = ModflowCalculationAggregate::create(
            $command->calculationId(),
            $modflowModel->modflowModelId(),
            $modflowModel->soilmodelId(),
            $modflowModel->ownerId(),
            $command->startDateTime(),
            $command->endDateTime(),
            $modflowModel->lengthUnit(),
            $modflowModel->timeUnit(),
            $stressPeriods
        );

        $this->modelCalculationList->add($calculation);
    }

    private function getModflowModel(CreateModflowModelCalculation $command): ModflowModelAggregate
    {
        /** @var ModflowModelAggregate $baseModel */
        $baseModel = $this->modelList->get($command->modflowModelId());

        if (!$baseModel instanceof ModflowModelAggregate){
            throw ModflowModelNotFoundException::withModelId($command->modflowModelId());
        }

        if ($command->scenarioId() instanceof ModflowId) {
            $scenario = $baseModel->findScenario($command->scenarioId());
            if (! $scenario instanceof ModflowModelAggregate){
                throw ModflowModelNotFoundException::withScenarioId($command->scenarioId(), $command->modflowModelId());
            }
            return $scenario;
        }

        return $baseModel;
    }
}
