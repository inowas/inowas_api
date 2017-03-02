<?php

namespace Inowas\Modflow\Model;


use Inowas\Common\DateTime\DateTime;
use Inowas\Common\FileName;
use Inowas\Common\LayerNumber;
use Inowas\Modflow\Model\Event\BudgetWasCalculated;
use Inowas\Modflow\Model\Event\HeadWasCalculated;
use Inowas\Modflow\Model\Event\CalculationWasCreated;
use Prooph\EventSourcing\AggregateRoot;

class ModflowCalculationAggregate extends AggregateRoot
{

    /** @var ModflowId */
    private $calculationId;

    /** @var ModflowId */
    private $modflowModelId;

    /** @var SoilModelId */
    private $soilModelId;

    /** @var  UserId */
    private $ownerId;

    /** @var ModflowModelGridSize */
    private $gridSize;

    /** @var  array */
    private $results;

    /** @var  DateTime */
    private $startDateTime;

    /** @var  DateTime */
    private $endDateTime;

    public static function create(
        ModflowId $calculationId,
        ModflowId $modflowModelId,
        SoilModelId $soilModelId,
        UserId $userId,
        ModflowModelGridSize $gridSize,
        DateTime $startDateTime,
        DateTime $endDateTime
    ): ModflowCalculationAggregate
    {
        $self = new self();
        $self->calculationId = $calculationId;
        $self->modflowModelId = $modflowModelId;
        $self->soilModelId = $soilModelId;
        $self->ownerId = $userId;
        $self->gridSize = $gridSize;
        $self->startDateTime = $startDateTime;
        $self->endDateTime = $endDateTime;

        $self->recordThat(
            CalculationWasCreated::fromModel(
                $userId,
                $calculationId,
                $modflowModelId,
                $soilModelId,
                $gridSize,
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

    public function addCalculatedBudget(TotalTime $totalTime, Budget $budget): void
    {
        $this->recordThat(BudgetWasCalculated::to($this->calculationId, $totalTime, $budget));
    }

    public function calculationId(): ModflowId
    {
        return $this->calculationId;
    }

    public function modelId(): ModflowId
    {
        return $this->modflowModelId;
    }

    public function soilModelId(): SoilModelId
    {
        return $this->soilModelId;
    }

    public function ownerId(): UserId
    {
        return $this->ownerId;
    }

    public function gridSize(): ModflowModelGridSize
    {
        return $this->gridSize;
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

    protected function whenCalculationWasCreated(CalculationWasCreated $event): void
    {
        $this->calculationId = $event->calculationId();
        $this->modflowModelId = $event->modflowModelId();
        $this->soilModelId = $event->soilModelId();
        $this->ownerId = $event->userId();
        $this->gridSize = $event->gridSize();
        $this->startDateTime = $event->startDateTime();
        $this->endDateTime = $event->endDateTime();
    }

    protected function whenHeadWasCalculated(HeadWasCalculated $event): void
    {
        #$this->mergeResult($event->result());
    }

    protected function whenBudgetWasCalculated(BudgetWasCalculated $event): void
    {

    }

    /**
     * @return string
     */
    protected function aggregateId(): string
    {
        return $this->calculationId->toString();
    }
}
