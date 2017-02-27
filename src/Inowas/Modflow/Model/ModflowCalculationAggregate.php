<?php

namespace Inowas\Modflow\Model;


use Inowas\Common\DateTime\DateTime;
use Inowas\Modflow\Model\Event\ModflowCalculationResultWasAdded;
use Inowas\Modflow\Model\Event\ModflowCalculationWasCreated;
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
            ModflowCalculationWasCreated::fromModel(
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

    public function addResult(CalculationResultWithFilename $result)
    {
        $this->recordThat(ModflowCalculationResultWasAdded::to($this->calculationId(), $result));
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

    protected function whenModflowCalculationWasCreated(ModflowCalculationWasCreated $event): void
    {
        $this->calculationId = $event->calculationId();
        $this->modflowModelId = $event->modflowModelId();
        $this->soilModelId = $event->soilModelId();
        $this->ownerId = $event->userId();
        $this->gridSize = $event->gridSize();
        $this->startDateTime = $event->startDateTime();
        $this->endDateTime = $event->endDateTime();
    }

    protected function whenModflowCalculationResultWasAdded(ModflowCalculationResultWasAdded $event): void
    {
        #$this->mergeResult($event->result());
    }

    protected function mergeResult(CalculationResultWithData $result): void
    {
        #$this->results[$result->type()->toString()][$result->totalTime()->toTotalTime()] = $result->data();
    }

    /**
     * @return string
     */
    protected function aggregateId(): string
    {
        return $this->calculationId->toString();
    }
}
