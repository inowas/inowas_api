<?php

declare(strict_types=1);

namespace Inowas\ModflowCalculation\Infrastructure\ProcessManager;

use Inowas\Common\Id\ModflowId;
use Inowas\ModflowCalculation\Infrastructure\Projection\Calculation\CalculationListFinder;
use Inowas\ModflowCalculation\Model\Command\CalculateModflowModelCalculation;
use Inowas\ModflowCalculation\Model\Command\UpdateCalculationWithChangedBoundaries;
use Inowas\ModflowModel\Model\Event\EditingBoundariesWasFinished;
use Prooph\ServiceBus\CommandBus;

final class UpdateModflowCalculationProcessManager
{

    /** @var  CalculationListFinder */
    private $calculationFinder;


    /** @var  CommandBus */
    private $commandBus;

    public function __construct(CommandBus $commandBus, CalculationListFinder $calculationFinder) {
        $this->calculationFinder = $calculationFinder;
        $this->commandBus = $commandBus;

    }

    public function onEditingBoundariesWasFinished(EditingBoundariesWasFinished $event): void
    {
        $calculationId = $this->calculationFinder->findLastCalculationByModelId($event->modflowModelId());

        if ($calculationId instanceof ModflowId) {
            $this->commandBus->dispatch(UpdateCalculationWithChangedBoundaries::byUserWithCalculationId($event->userId(), $calculationId));
            $this->commandBus->dispatch(CalculateModflowModelCalculation::byUserWithCalculationId($event->userId(), $calculationId));
        }
    }
}
