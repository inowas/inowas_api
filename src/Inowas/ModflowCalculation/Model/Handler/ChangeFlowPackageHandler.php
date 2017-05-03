<?php

declare(strict_types=1);

namespace Inowas\ModflowCalculation\Model\Handler;

use Inowas\ModflowCalculation\Model\Command\ChangeFlowPackage;
use Inowas\ModflowCalculation\Model\ModflowCalculationAggregate;
use Inowas\ModflowCalculation\Model\ModflowCalculationList;
use Inowas\Soilmodel\Model\Exception\CalculationNotFoundException;

final class ChangeFlowPackageHandler
{

    /** @var  ModflowCalculationList */
    private $calculationList;

    /** @param ModflowCalculationList $calculationList */
    public function __construct(ModflowCalculationList $calculationList)
    {
        $this->calculationList = $calculationList;
    }

    public function __invoke(ChangeFlowPackage $command)
    {

        /** @var ModflowCalculationAggregate $calculation */
        $calculation = $this->calculationList->get($command->calculationId());

        if (!$calculation){
            throw CalculationNotFoundException::withId($command->calculationId());
        }

        $calculation->changeFlowPackage($command->userId(), $command->packageName());
    }
}
