<?php

namespace Inowas\ModflowModel\Infrastructure\ProcessManager;

use Inowas\ModflowModel\Model\Command\UpdateCalculationId;
use Inowas\ModflowModel\Model\Event\FlowPackageWasChanged;
use Inowas\ModflowModel\Model\Event\LengthUnitWasUpdated;
use Inowas\ModflowModel\Model\Event\ModflowModelWasCloned;
use Inowas\ModflowModel\Model\Event\ModflowModelWasCreated;
use Inowas\ModflowModel\Model\Event\ModflowPackageParameterWasUpdated;
use Inowas\ModflowModel\Model\Event\SoilModelWasChanged;
use Inowas\ModflowModel\Model\Event\StressPeriodsWereUpdated;
use Inowas\ModflowModel\Model\Event\TimeUnitWasUpdated;
use Inowas\ModflowModel\Service\ModflowPackagesManager;
use Prooph\ServiceBus\CommandBus;

class ModflowPackagesProcessManager
{
    /** @var  CommandBus */
    private $commandBus;

    /** @var  ModflowPackagesManager */
    private $packagesManager;

    public function __construct(CommandBus $commandBus, ModflowPackagesManager $packagesManager)
    {
        $this->commandBus = $commandBus;
        $this->packagesManager = $packagesManager;
    }

    public function onFlowPackageWasChanged(FlowPackageWasChanged $event): void
    {
        $packages = $this->packagesManager->loadByModelId($event->modelId());
        $packages->changeFlowPackage($event->packageName());
        $calculationId = $this->packagesManager->save($packages);
        $this->commandBus->dispatch(UpdateCalculationId::withId($event->modelId(), $calculationId));
    }

    public function onLengthUnitWasUpdated(LengthUnitWasUpdated $event): void
    {
        $packages = $this->packagesManager->loadByModelId($event->modelId());
        $packages->updateLengthUnit($event->lengthUnit());
        $calculationId = $this->packagesManager->save($packages);
        $this->commandBus->dispatch(UpdateCalculationId::withId($event->modelId(), $calculationId));
    }

    public function onModflowModelWasCloned(ModflowModelWasCloned $event): void
    {
        $calculationId = $this->packagesManager->getCalculationId($event->baseModelId());
        $this->commandBus->dispatch(UpdateCalculationId::withId($event->modelId(), $calculationId));
    }

    public function onModflowModelWasCreated(ModflowModelWasCreated $event): void
    {
        $calculationId = $this->packagesManager->createFromDefaultsAndSave();
        $this->commandBus->dispatch(UpdateCalculationId::withId($event->modelId(), $calculationId));
    }

    public function onSoilModelWasChanged(SoilModelWasChanged $event): void
    {
        $calculationId = $this->packagesManager->recalculateAfterSoilmodelWasChanged($event->modflowModelId(), $event->soilModelId());
        $this->commandBus->dispatch(UpdateCalculationId::withId($event->modflowModelId(), $calculationId));
    }

    public function onStressPeriodsWereUpdated(StressPeriodsWereUpdated $event): void
    {
        $calculationId = $this->packagesManager->recalculateAfterStressperiodsWereChanged($event->modelId(), $event->stressPeriods());
        $this->commandBus->dispatch(UpdateCalculationId::withId($event->modelId(), $calculationId));
    }

    public function onTimeUnitWasUpdated(TimeUnitWasUpdated $event): void
    {
        $packages = $this->packagesManager->loadByModelId($event->modelId());
        $packages->updateTimeUnit($event->timeUnit());
        $calculationId = $this->packagesManager->save($packages);
        $this->commandBus->dispatch(UpdateCalculationId::withId($event->modelId(), $calculationId));
    }

    public function onModflowPackageParameterWasUpdated(ModflowPackageParameterWasUpdated $event): void
    {
        $packages = $this->packagesManager->loadByModelId($event->modelId());
        $packages->updatePackageParameter($event->packageName()->toString(), $event->parameterName()->toString(), $event->parameterData());
        $calculationId = $this->packagesManager->save($packages);
        $this->commandBus->dispatch(UpdateCalculationId::withId($event->modelId(), $calculationId));
    }
}
