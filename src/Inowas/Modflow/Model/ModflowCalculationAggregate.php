<?php

namespace Inowas\Modflow\Model;


use Inowas\Common\Calculation\Budget;
use Inowas\Common\Calculation\BudgetType;
use Inowas\Common\Calculation\ResultType;
use Inowas\Common\DateTime\DateTime;
use Inowas\Common\DateTime\TotalTime;
use Inowas\Common\FileSystem\FileName;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\DeltaCol;
use Inowas\Common\Grid\DeltaRow;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Grid\LayerNumber;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Modflow\Model\Event\BudgetWasCalculated;
use Inowas\Modflow\Model\Event\HeadWasCalculated;
use Inowas\Modflow\Model\Event\CalculationWasCreated;
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

    /** @var  ModflowModelStressperiods */
    private $stressperiods;

    /** @var  array */
    private $results;

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
        BoundingBox $boundingBox,
        GridSize $gridSize,
        TimeUnit $timeUnit,
        LengthUnit $lengthUnit,
        DateTime $startDateTime,
        DateTime $endDateTime
    ): ModflowCalculationAggregate
    {
        $self = new self();
        $self->calculationId = $calculationId;
        $self->modflowModelId = $modflowModelId;
        $self->soilModelId = $soilModelId;
        $self->ownerId = $userId;
        $self->boundingBox = $boundingBox;
        $self->gridSize = $gridSize;
        $self->lengthUnit = $lengthUnit;
        $self->timeUnit = $timeUnit;
        $self->startDateTime = $startDateTime;
        $self->endDateTime = $endDateTime;

        $self->recordThat(
            CalculationWasCreated::fromModel(
                $userId,
                $calculationId,
                $modflowModelId,
                $soilModelId,
                $gridSize,
                $boundingBox,
                $timeUnit,
                $lengthUnit,
                $startDateTime,
                $endDateTime
            )
        );

        return $self;
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

    public function delCol(): DeltaCol
    {
        return DeltaCol::fromValue($this->boundingBox->dX()/$this->gridSize()->nX());
    }

    public function delRow(): DeltaRow
    {
        return DeltaRow::fromValue($this->boundingBox->dY()/$this->gridSize()->nY());
    }

    public function stressPeriods(): ModflowModelStressperiods
    {
        return $this->stressperiods;
    }

    public function results(): array
    {
        return $this->results;
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
        $this->gridSize = $event->gridSize();
        $this->boundingBox = $event->boundingBox();
        $this->timeUnit = $event->timeUnit();
        $this->lengthUnit = $event->lengthUnit();
        $this->startDateTime = $event->startDateTime();
        $this->endDateTime = $event->endDateTime();
        $this->packages = Packages::createFromDefaults();
    }

    protected function whenHeadWasCalculated(HeadWasCalculated $event): void
    {}

    protected function whenBudgetWasCalculated(BudgetWasCalculated $event): void
    {}

    /**
     * @return string
     */
    protected function aggregateId(): string
    {
        return $this->calculationId->toString();
    }
}
