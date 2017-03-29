<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Handler;

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
        $modflowModel = $this->modelList->get($command->modflowModelId());

        if (!$modflowModel){
            throw ModflowModelNotFoundException::withModelId($command->modflowModelId());
        }

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
    }
}
