<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Handler;

use Inowas\Modflow\Model\Command\AddResultToCalculation;
use Inowas\Modflow\Model\Exception\ModflowCalculationNotFoundException;
use Inowas\Modflow\Model\ModflowCalculationAggregate;
use Inowas\Modflow\Model\ModflowModelCalculationList;

final class AddResultToCalculationHandler
{

    /** @var  ModflowModelCalculationList */
    private $calculationList;


    public function __construct(ModflowModelCalculationList $calculationList)
    {
        $this->calculationList = $calculationList;
    }

    public function __invoke(AddResultToCalculation $command)
    {
        /** @var ModflowCalculationAggregate $calculation */
        $calculation = $this->calculationList->get($command->calculationId());

        if (!$calculation){
            throw ModflowCalculationNotFoundException::withId($command->calculationId());
        }

        $calculation->addResult($command->result());
    }
}
