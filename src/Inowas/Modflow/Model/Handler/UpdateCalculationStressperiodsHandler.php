<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Handler;

use Inowas\Modflow\Model\Command\UpdateCalculationStressperiods;
use Inowas\Modflow\Model\ModflowCalculationAggregate;
use Inowas\Modflow\Model\ModflowModelCalculationList;
use Inowas\Soilmodel\Model\Exception\CalculationNotFoundException;

final class UpdateCalculationStressperiodsHandler
{

    /** @var  ModflowModelCalculationList */
    private $calculationList;

    /**
     * UpdateCalculationResultsHandler constructor.
     * @param ModflowModelCalculationList $calculationList
     */
    public function __construct(ModflowModelCalculationList $calculationList)
    {
        $this->calculationList = $calculationList;
    }

    public function __invoke(UpdateCalculationStressperiods $command)
    {

        /** @var ModflowCalculationAggregate $calculation */
        $calculation = $this->calculationList->get($command->calculationId());

        if (!$calculation){
            throw CalculationNotFoundException::withId($command->calculationId());
        }

        $calculation->updateStressperiods($command->userId(), $command->stressPeriods());
    }
}