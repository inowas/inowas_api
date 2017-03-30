<?php

namespace Inowas\Modflow\Model;

use Inowas\Common\Calculation\Budget;
use Inowas\Common\Calculation\BudgetType;
use Inowas\Common\Calculation\ResultType;
use Inowas\Common\DateTime\DateTime;
use Inowas\Common\DateTime\TotalTime;
use Inowas\Common\FileSystem\FileName;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Grid\LayerNumber;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\IBound;
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\Strt;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Modflow\Model\Event\BudgetWasCalculated;
use Inowas\Modflow\Model\Event\EndDateTimeWasUpdated;
use Inowas\Modflow\Model\Event\ExecutableNameWasUpdated;
use Inowas\Modflow\Model\Event\GridParameterWereUpdated;
use Inowas\Modflow\Model\Event\HeadWasCalculated;
use Inowas\Modflow\Model\Event\CalculationWasCreated;
use Inowas\Modflow\Model\Event\IBoundWasUpdated;
use Inowas\Modflow\Model\Event\LengthUnitWasUpdated;
use Inowas\Modflow\Model\Event\ModflowModelNameWasUpdated;
use Inowas\Modflow\Model\Event\StartDateTimeWasUpdated;
use Inowas\Modflow\Model\Event\StrtWasUpdated;
use Inowas\Modflow\Model\Event\TimeUnitWasUpdated;
use Inowas\Modflow\Model\Packages\Packages;
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

    /** @var GridSize */
    private $gridSize;

    /** @var  BoundingBox */
    private $boundingBox;

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
        UserId $userId
    ): ModflowCalculationAggregate
    {
        $self = new self();
        $self->calculationId = $calculationId;
        $self->modflowModelId = $modflowModelId;
        $self->soilModelId = $soilModelId;
        $self->ownerId = $userId;

        $self->recordThat(
            CalculationWasCreated::fromModel(
                $userId,
                $calculationId,
                $modflowModelId,
                $soilModelId
            )
        );

        return $self;
    }

    public function updateModelName(ModflowModelName $name): void
    {
        $this->packages->updateModelName($name);
        $this->recordThat(ModflowModelNameWasUpdated::to($this->calculationId, $name));
    }

    public function updateExecutableName(FileName $name): void
    {
        $this->packages->updateExecutableName($name);
        $this->recordThat(ExecutableNameWasUpdated::to($this->calculationId, $name));
    }

    public function updateGridParameters(GridSize $gridSize, BoundingBox $boundingBox)
    {
        if (is_null($this->gridSize) || (!$this->gridSize->sameAs($gridSize)) ||
            is_null($this->boundingBox) || (!$this->boundingBox->sameAs($boundingBox))
        ) {
            $this->gridSize = $gridSize;
            $this->boundingBox = $boundingBox;
            $this->packages->updateGridParameters($gridSize, $boundingBox);
            $this->recordThat(GridParameterWereUpdated::to($this->calculationId(), $gridSize, $boundingBox));
        }
    }

    public function updateTimeUnit(TimeUnit $timeUnit): void
    {
        if (is_null($this->timeUnit) || (!$this->timeUnit->sameAs($timeUnit))) {
            $this->timeUnit = $timeUnit;
            $this->packages->updateTimeUnit($timeUnit);
            $this->recordThat(TimeUnitWasUpdated::to($this->calculationId, $timeUnit));
        }
    }

    public function updateLengthUnit(LengthUnit $lengthUnit): void
    {
        if (is_null($this->lengthUnit) || (!$this->lengthUnit->sameAs($lengthUnit))) {
            $this->lengthUnit = $lengthUnit;
            $this->packages->updateLengthUnit($lengthUnit);
            $this->recordThat(LengthUnitWasUpdated::to($this->calculationId, $lengthUnit));
        }
    }

    public function updateStartDateTime(DateTime $start): void
    {
        $this->startDateTime = $start;
        $this->packages->updateStartDateTime($start);
        $this->recordThat(StartDateTimeWasUpdated::to($this->calculationId, $start));
    }

    public function updateEndDateTime(DateTime $end): void
    {
        $this->endDateTime = $end;
        $this->recordThat(EndDateTimeWasUpdated::to($this->calculationId, $end));
    }

    public function updateIBound(IBound $iBound): void
    {
        $this->packages->updateIBound($iBound);
        $this->recordThat(IBoundWasUpdated::to($this->calculationId, $iBound));
    }

    public function updateStrt(Strt $strt): void
    {
        $this->packages->updateStrt($strt);
        $this->recordThat(StrtWasUpdated::to($this->calculationId, $strt));
    }

    public function addCalculatedHead(ResultType $type, TotalTime $totalTime, LayerNumber $layerNumber, FileName $fileName): void
    {
        $this->recordThat(HeadWasCalculated::to($this->calculationId, $type, $totalTime, $layerNumber, $fileName));
    }

    public function addCalculatedBudget(TotalTime $totalTime, Budget $budget, BudgetType $budgetType): void
    {
        $this->recordThat(BudgetWasCalculated::to($this->calculationId, $totalTime, $budget, $budgetType));
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

    public function gridSize(): GridSize
    {
        return $this->gridSize;
    }

    public function boundingBox(): BoundingBox
    {
        return $this->boundingBox;
    }

    public function startDateTime(): DateTime
    {
        return $this->startDateTime;
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

    protected function whenCalculationWasCreated(CalculationWasCreated $event): void
    {
        $this->calculationId = $event->calculationId();
        $this->modflowModelId = $event->modflowModelId();
        $this->soilModelId = $event->soilModelId();
        $this->ownerId = $event->userId();
        $this->packages = Packages::createFromDefaults();
    }

    protected function whenBudgetWasCalculated(BudgetWasCalculated $event): void
    {
    }

    protected function whenGridParameterWereUpdated(GridParameterWereUpdated $event): void
    {
        $this->boundingBox = $event->boundingBox();
        $this->gridSize = $event->gridSize();
        $this->packages->updateGridParameters($event->gridSize(), $event->boundingBox());
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

    protected function whenIBoundWasUpdated(IBoundWasUpdated $event): void
    {
        $this->packages->updateIBound($event->iBound());
    }

    protected function whenStrtWasUpdated(StrtWasUpdated $event): void
    {
        $this->packages->updateStrt($event->strt());
    }

    protected function whenModflowModelNameWasUpdated(ModflowModelNameWasUpdated $event): void
    {
        $this->packages->updateModelName($event->name());
    }

    protected function whenExecutableNameWasUpdated(ExecutableNameWasUpdated $event): void
    {
        $this->packages->updateExecutableName($event->name());
    }

    /**
     * @return string
     */
    protected function aggregateId(): string
    {
        return $this->calculationId->toString();
    }
}
