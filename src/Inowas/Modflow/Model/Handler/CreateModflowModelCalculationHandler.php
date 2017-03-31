<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Handler;

use Inowas\Common\Id\ModflowId;
use Inowas\Modflow\Model\Command\CreateModflowModelCalculation;
use Inowas\Modflow\Model\Exception\ModflowModelNotFoundException;
use Inowas\Modflow\Model\ModflowCalculationAggregate;
use Inowas\Modflow\Model\ModflowModelCalculationList;
use Inowas\Modflow\Model\ModflowModelList;
use Inowas\Modflow\Model\ModflowModelAggregate;
use Inowas\Soilmodel\Model\SoilmodelList;

final class CreateModflowModelCalculationHandler
{

    /** @var  ModflowModelList */
    private $modelList;

    /** @var  ModflowModelList */
    private $modelCalculationList;

    /** @var  SoilmodelList */
    private $soilmodelList;

    public function __construct(ModflowModelList $modelList, SoilmodelList $soilmodelList, ModflowModelCalculationList $modelCalculationList)
    {
        $this->modelCalculationList = $modelCalculationList;
        $this->modelList = $modelList;
        $this->soilmodelList = $soilmodelList;
    }

    public function __invoke(CreateModflowModelCalculation $command)
    {
        /** @var ModflowModelAggregate $modflowModel */
        $modflowModel = $this->getModflowModel($command);

        $calculation = ModflowCalculationAggregate::create(
            $command->calculationId(),
            $modflowModel->modflowModelId(),
            $modflowModel->soilmodelId(),
            $modflowModel->ownerId(),
            $command->startDateTime(),
            $command->endDateTime(),
            $modflowModel->lengthUnit(),
            $modflowModel->timeUnit()
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
