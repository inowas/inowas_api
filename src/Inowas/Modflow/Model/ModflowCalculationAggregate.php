<?php

namespace Inowas\Modflow\Model;

use Inowas\Common\Calculation\Budget;
use Inowas\Common\Calculation\BudgetType;
use Inowas\Common\Calculation\ResultType;
use Inowas\Common\DateTime\DateTime;
use Inowas\Common\DateTime\TotalTime;
use Inowas\Common\FileSystem\FileName;
use Inowas\Common\Grid\LayerNumber;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Modflow\Model\Event\BudgetWasCalculated;
use Inowas\Modflow\Model\Event\CalculationPackageParameterWasUpdated;
use Inowas\Modflow\Model\Event\CalculationWasFinished;
use Inowas\Modflow\Model\Event\CalculationWasQueued;
use Inowas\Modflow\Model\Event\CalculationWasStarted;
use Inowas\Modflow\Model\Event\EndDateTimeWasUpdated;
use Inowas\Modflow\Model\Event\HeadWasCalculated;
use Inowas\Modflow\Model\Event\CalculationWasCreated;
use Inowas\Modflow\Model\Event\LengthUnitWasUpdated;
use Inowas\Modflow\Model\Event\StartDateTimeWasUpdated;
use Inowas\Modflow\Model\Event\TimeUnitWasUpdated;
use Inowas\Modflow\Model\Packages\Packages;
use Inowas\Soilmodel\Interpolation\FlopyCalculationResponse;
use Inowas\Soilmodel\Model\SoilmodelId;
use Prooph\EventSourcing\AggregateRoot;


class ModflowCalculationAggregate extends AggregateRoot
{
    /** @var ModflowId */
    private $calculationId;

    /** @var ModflowId */
    private $modflowModelId;

    /** @var SoilmodelId */
    private $soilModelId;

    /** @var  UserId */
    private $ownerId;

    /** @var  TimeUnit */
    private $timeUnit;

    /** @var  LengthUnit */
    private $lengthUnit;

    /** @var  DateTime */
    private $startDateTime;

    /** @var  DateTime */
    private $endDateTime;

    /** @var Packages */
    private $packages;

    public static function create(
        ModflowId $calculationId,
        ModflowId $modflowModelId,
        SoilmodelId $soilModelId,
        UserId $userId,
        DateTime $start,
        DateTime $end,
        LengthUnit $lengthUnit,
        TimeUnit $timeUnit
    ): ModflowCalculationAggregate
    {
        $self = new self();
        $self->calculationId = $calculationId;
        $self->modflowModelId = $modflowModelId;
        $self->soilModelId = $soilModelId;
        $self->ownerId = $userId;
        $self->startDateTime = $start;
        $self->endDateTime = $end;
        $self->lengthUnit = $lengthUnit;
        $self->timeUnit = $timeUnit;
        $self->packages = Packages::createFromDefaultsWithId($calculationId);

        $self->recordThat(
            CalculationWasCreated::fromModelWithProps(
                $userId,
                $calculationId,
                $modflowModelId,
                $soilModelId,
                $start,
                $end,
                $lengthUnit,
                $timeUnit
            )
        );

        return $self;
    }

    public function updateStartDateTime(DateTime $start): void
    {
        $this->startDateTime = $start;
        $this->recordThat(StartDateTimeWasUpdated::to($this->calculationId, $start));
    }

    public function updateEndDateTime(DateTime $end): void
    {
        $this->endDateTime = $end;
        $this->recordThat(EndDateTimeWasUpdated::to($this->calculationId, $end));
    }

    public function updateLengthUnit(LengthUnit $lengthUnit): void
    {
        if (! $this->lengthUnit->sameAs($lengthUnit)){
            $this->lengthUnit = $lengthUnit;
            $this->recordThat(LengthUnitWasUpdated::to($this->calculationId, $lengthUnit));
        }
    }

    public function updateTimeUnit(TimeUnit $timeUnit): void
    {
        if (! $this->timeUnit->sameAs($timeUnit)){
            $this->timeUnit = $timeUnit;
            $this->recordThat(TimeUnitWasUpdated::to($this->calculationId, $timeUnit));
        }
    }

    public function updatePackageParameter(UserId $userId, string $packageName, string $parameterName, $parameterData): void
    {
        $this->packages->updatePackageParameter($packageName, $parameterName, $parameterData);

        $this->recordThat(CalculationPackageParameterWasUpdated::withProps(
            $userId,
            $this->calculationId,
            $packageName,
            $parameterName,
            $parameterData
        ));
    }

    public function addCalculatedHead(ResultType $type, TotalTime $totalTime, LayerNumber $layerNumber, FileName $fileName): void
    {
        $this->recordThat(HeadWasCalculated::to($this->calculationId, $type, $totalTime, $layerNumber, $fileName));
    }

    public function addCalculatedBudget(TotalTime $totalTime, Budget $budget, BudgetType $budgetType): void
    {
        $this->recordThat(BudgetWasCalculated::to($this->calculationId, $totalTime, $budget, $budgetType));
    }

    public function calculationHasQueued(): void
    {
        $this->recordThat(CalculationWasQueued::withId($this->calculationId));
    }

    public function calculationHasStarted(): void
    {
        $this->recordThat(CalculationWasStarted::withId($this->calculationId));
    }

    public function calculationHasFinished(FlopyCalculationResponse $response): void
    {
        $this->recordThat(CalculationWasFinished::withIdAndResponse($this->calculationId, $response));
    }

    public function calculationId(): ModflowId
    {
        return $this->calculationId;
    }

    public function modelId(): ModflowId
    {
        return $this->modflowModelId;
    }

    public function soilModelId(): SoilmodelId
    {
        return $this->soilModelId;
    }

    public function ownerId(): UserId
    {
        return $this->ownerId;
    }

    public function endDateTime(): DateTime
    {
        return $this->endDateTime;
    }

    public function lengthUnit(): LengthUnit
    {
        return $this->lengthUnit;
    }

    public function timeUnit(): TimeUnit
    {
        return $this->timeUnit;
    }

    public function packages(): Packages
    {
        return $this->packages;
    }

    protected function whenCalculationPackageParameterWasUpdated(CalculationPackageParameterWasUpdated $event): void
    {
        $this->packages->updatePackageParameter($event->packageName(), $event->parameterName(), $event->parameterData());
    }

    protected function whenCalculationWasCreated(CalculationWasCreated $event): void
    {
        $this->calculationId = $event->calculationId();
        $this->modflowModelId = $event->modflowModelId();
        $this->soilModelId = $event->soilModelId();
        $this->ownerId = $event->userId();
        $this->startDateTime = $event->start();
        $this->endDateTime = $event->end();
        $this->lengthUnit = $event->lengthUnit();
        $this->timeUnit = $event->timeUnit();
        $this->packages = Packages::createFromDefaultsWithId($event->calculationId());
    }

    protected function whenCalculationWasQueued(CalculationWasQueued $event): void
    {}

    protected function whenCalculationWasStarted(CalculationWasStarted $event): void
    {}

    protected function whenCalculationWasFinished(CalculationWasFinished $event): void
    {}

    protected function whenBudgetWasCalculated(BudgetWasCalculated $event): void
    {
    }

    protected function whenHeadWasCalculated(HeadWasCalculated $event): void
    {
    }

    protected function whenLengthUnitWasUpdated(LengthUnitWasUpdated $event): void
    {
        $this->lengthUnit = $event->lengthUnit();
        $this->packages->updateLengthUnit($event->lengthUnit());
    }

    protected function whenTimeUnitWasUpdated(TimeUnitWasUpdated $event): void
    {
        $this->timeUnit = $event->timeUnit();
        $this->packages->updateTimeUnit($event->timeUnit());
    }

    protected function whenStartDateTimeWasUpdated(StartDateTimeWasUpdated $event): void
    {
        $this->startDateTime = $event->start();
        $this->packages->updateStartDateTime($event->start());
    }

    protected function whenEndDateTimeWasUpdated(EndDateTimeWasUpdated $event): void
    {
        $this->endDateTime = $event->end();
    }

    protected function aggregateId(): string
    {
        return $this->calculationId->toString();
    }
}
