<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Soilmodel\Model\SoilmodelId;
use Prooph\EventSourcing\AggregateChanged;

class CalculationWasCreated extends AggregateChanged
{
    /** @var  ModflowId */
    private $calculationId;

    /** @var  ModflowId */
    private $modflowModelId;

    /** @var  SoilmodelId */
    private $soilModelId;

    /** @var UserId */
    private $userId;

    public static function fromModel(
        UserId $userId,
        ModflowId $calculationId,
        ModflowId $modflowModelId,
        SoilmodelId $soilModelId
    ): CalculationWasCreated
    {
        $event = self::occur($calculationId->toString(),[
            'user_id' => $userId->toString(),
            'modflowmodel_id' => $modflowModelId->toString(),
            'soilmodel_id' => $soilModelId->toString()
        ]);

        $event->calculationId = $calculationId;
        $event->modflowModelId = $modflowModelId;
        $event->soilModelId = $soilModelId;
        $event->userId = $userId;

        return $event;
    }

    public function calculationId(): ModflowId
    {
        if ($this->calculationId === null){
            $this->calculationId = ModflowId::fromString($this->aggregateId());
        }

        return $this->calculationId;
    }

    public function modflowModelId(): ModflowId
    {
        if ($this->modflowModelId === null){
            $this->modflowModelId = ModflowId::fromString($this->payload['modflowmodel_id']);
        }

        return $this->modflowModelId;
    }

    public function soilModelId(): SoilmodelId
    {
        if ($this->soilModelId === null){
            $this->soilModelId = SoilmodelId::fromString($this->payload['soilmodel_id']);
        }

        return $this->soilModelId;
    }

    public function userId(): UserId{
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }
}
