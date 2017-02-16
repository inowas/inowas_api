<?php

namespace Inowas\Modflow\Model;


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

    public static function create(ModflowId $calculationId, ModflowId $modflowModelId, SoilModelId $soilModelId, UserId $userId): ModflowCalculationAggregate
    {
        $self = new self();
        $self->calculationId = $calculationId;
        $self->modflowModelId = $modflowModelId;
        $self->soilModelId = $soilModelId;
        $self->ownerId = $userId;

        $self->recordThat(ModflowCalculationWasCreated::fromModel($userId, $calculationId, $soilModelId, $modflowModelId));
        return $self;
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

    protected function whenModflowCalculationWasCreated(ModflowCalculationWasCreated $event){
        $this->calculationId = $event->calculationId();
        $this->modflowModelId = $event->modflowModelId();
        $this->soilModelId = $event->soilModelId();
        $this->ownerId = $event->userId();
    }

    /**
     * @return mixed
     */
    protected function aggregateId(): string
    {
        return $this->calculationId->toString();
    }
}
