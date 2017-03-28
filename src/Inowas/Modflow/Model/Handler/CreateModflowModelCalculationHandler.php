<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Handler;

use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Modflow\Model\Command\CreateModflowModelCalculation;
use Inowas\Modflow\Model\Exception\ModflowModelNotFoundException;
use Inowas\Modflow\Model\ModflowCalculationAggregate;
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

        /**
         * @TODO Fix this and get the units from the userProfile
         */
        $timeUnit = TimeUnit::fromValue(TimeUnit::DAYS);
        $lengthUnit = LengthUnit::fromValue(LengthUnit::METERS);

        /** @var ModflowModelAggregate $modflowModel */
        $modflowModel = $this->modelList->get($command->modflowModelId());

        if (!$modflowModel){
            throw ModflowModelNotFoundException::withModelId($command->modflowModelId());
        }

        $calculationId = $command->calculationId();

        if (is_null($command->scenarioId())){
            $calculation = $modflowModel->createCalculationFromBaseModel($calculationId, $timeUnit, $lengthUnit, $command->startDateTime(), $command->endDateTime());
        } else {
            $calculation = $modflowModel->createCalculationFromScenario($calculationId, $command->scenarioId(), $timeUnit, $lengthUnit, $command->startDateTime(), $command->endDateTime());
        }

        if ($calculation instanceof ModflowCalculationAggregate) {
            $this->modelCalculationList->add($calculation);
        }
    }
}
