<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Handler;

use Inowas\Modflow\Model\Command\AddCalculatedHead;
use Inowas\Modflow\Model\Exception\ModflowCalculationNotFoundException;
use Inowas\Modflow\Model\ModflowCalculationAggregate;
use Inowas\Modflow\Model\ModflowModelCalculationList;
use Inowas\ModflowBundle\Service\FilePersister;

final class AddCalculatedHeadHandler
{
    /** @var  ModflowModelCalculationList */
    private $calculationList;

    /** @var  FilePersister */
    private $persister;


    public function __construct(ModflowModelCalculationList $calculationList, FilePersister $persister)
    {
        $this->calculationList = $calculationList;
        $this->persister = $persister;
    }

    public function __invoke(AddCalculatedHead $command)
    {
        /** @var ModflowCalculationAggregate $calculation */
        $calculation = $this->calculationList->get($command->calculationId());

        if (!$calculation){
            throw ModflowCalculationNotFoundException::withId($command->calculationId());
        }

        $filename = $this->persister->persist(
            $calculation->calculationId(),
            $command->type(),
            $command->totalTime(),
            $command->layerNumber(),
            $command->data(),
            'json');

        $calculation->addCalculatedHead($command->type(), $command->totalTime(), $command->layerNumber(), $filename);
    }
}
