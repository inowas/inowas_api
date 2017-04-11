<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Handler;

use Inowas\Modflow\Model\Command\UpdateCalculationResults;
use Inowas\Modflow\Model\ModflowCalculationAggregate;
use Inowas\Modflow\Model\ModflowModelCalculationList;
use Inowas\Soilmodel\Model\Exception\CalculationNotFoundException;

final class UpdateCalculationResultsHandler
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

    public function __invoke(UpdateCalculationResults $command)
    {

        /** @var ModflowCalculationAggregate $calculation */
        $calculation = $this->calculationList->get($command->calculationId());

        if (!$calculation){
            throw CalculationNotFoundException::withId($command->calculationId());
        }

        $calculation->calculationHasFinished($command->response());
    }
}
