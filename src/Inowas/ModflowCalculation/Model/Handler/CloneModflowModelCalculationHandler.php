<?php

declare(strict_types=1);

namespace Inowas\ModflowCalculation\Model\Handler;

use Inowas\ModflowCalculation\Model\Command\CloneModflowModelCalculation;
use Inowas\ModflowCalculation\Model\ModflowCalculationAggregate;
use Inowas\ModflowCalculation\Model\ModflowCalculationList;
use Inowas\Soilmodel\Model\Exception\CalculationNotFoundException;

final class CloneModflowModelCalculationHandler
{
    /** @var  ModflowCalculationList */
    private $calculationList;

    public function __construct(ModflowCalculationList $calculationList) {
        $this->calculationList = $calculationList;
    }

    public function __invoke(CloneModflowModelCalculation $command)
    {

        /** @var ModflowCalculationAggregate $calculation */
        $calculation = $this->calculationList->get($command->oldCalculationId());

        if (!$calculation){
            throw CalculationNotFoundException::withId($command->oldCalculationId());
        }

        $newCalculation = ModflowCalculationAggregate::clone($command->newCalculationId(), $command->newModelId(), $command->userId(), $calculation);

        $this->calculationList->add($newCalculation);
    }
}
