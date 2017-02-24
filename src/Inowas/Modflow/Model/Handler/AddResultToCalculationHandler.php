<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Handler;

use Inowas\Modflow\Model\CalculationResultWithFilename;
use Inowas\Modflow\Model\Command\AddResultToCalculation;
use Inowas\Modflow\Model\Exception\ModflowCalculationNotFoundException;
use Inowas\Modflow\Model\ModflowCalculationAggregate;
use Inowas\Modflow\Model\ModflowModelCalculationList;
use Inowas\ModflowBundle\Service\CalculationResultsPersister;

final class AddResultToCalculationHandler
{

    /** @var  ModflowModelCalculationList */
    private $calculationList;

    /** @var  CalculationResultsPersister */
    private $persister;


    public function __construct(ModflowModelCalculationList $calculationList, CalculationResultsPersister $persister)
    {
        $this->calculationList = $calculationList;
        $this->persister = $persister;
    }

    public function __invoke(AddResultToCalculation $command)
    {
        /** @var ModflowCalculationAggregate $calculation */
        $calculation = $this->calculationList->get($command->calculationId());

        if (!$calculation){
            throw ModflowCalculationNotFoundException::withId($command->calculationId());
        }

        $filename = $this->persister->persist($calculation->calculationId(), $command->result(), 'json');


        $calculationResultWithFilename = CalculationResultWithFilename::fromParameters(
            $command->result()->type(),
            $command->result()->totalTime(),
            $command->result()->layerNumber(),
            $filename
        );

        $calculation->addResult($calculationResultWithFilename);
    }
}
