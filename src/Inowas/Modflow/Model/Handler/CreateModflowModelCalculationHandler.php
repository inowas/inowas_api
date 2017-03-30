<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Handler;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Modflow\Model\Command\CreateModflowModelCalculation;
use Inowas\Modflow\Model\Exception\ModflowModelNotFoundException;
use Inowas\Modflow\Model\ModflowModelCalculationList;
use Inowas\Modflow\Model\ModflowModelList;
use Inowas\Modflow\Model\ModflowModelAggregate;

final class CreateModflowModelCalculationHandler
{

    /** @var  ModflowModelList */
    private $modelList;

    /** @var  ModflowModelList */
    private $modelCalculationList;

    /**
     * CreateModflowModelCalculationHandler constructor.
     * @param ModflowModelList $modelList
     * @param ModflowModelCalculationList $modelCalculationList
     */
    public function __construct(ModflowModelList $modelList, ModflowModelCalculationList $modelCalculationList)
    {
        $this->modelList = $modelList;
        $this->modelCalculationList = $modelCalculationList;
    }

    public function __invoke(CreateModflowModelCalculation $command)
    {
        /** @var ModflowModelAggregate $modflowModel */
        $modflowModel = $this->getModflowModel($command);
        $calculationId = $command->calculationId();
        $calculation = $modflowModel->createCalculation($calculationId, $command->scenarioId());
        $this->modelCalculationList->add($calculation);

        /**
         * @TODO
         * Get the units from the userProfile
         */
        $timeUnit = TimeUnit::fromValue(TimeUnit::DAYS);
        $lengthUnit = LengthUnit::fromValue(LengthUnit::METERS);
        $startTime = $command->startDateTime();
        $endTime = $command->endDateTime();

        $calculation->updateGridParameters($modflowModel->gridSize(), $modflowModel->boundingBox());
        $calculation->updateTimeUnit($timeUnit);
        $calculation->updateLengthUnit($lengthUnit);
        $calculation->updateStartDateTime($startTime);
        $calculation->updateEndDateTime($endTime);
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
