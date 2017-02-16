<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Modflow\Model\ModflowId;
use Inowas\Modflow\Model\SoilModelId;
use Inowas\Modflow\Model\UserId;
use Prooph\EventSourcing\AggregateChanged;

class ModflowCalculationWasCreated extends AggregateChanged
{
    /** @var  ModflowId */
    private $calculationId;

    /** @var  ModflowId */
    private $modflowModelId;

    /** @var  SoilModelId */
    private $soilModelId;

    /** @var  UserId */
    private $userId;

    public static function fromModel(UserId $userId, ModflowId $calculationId, SoilModelId $soilModelId, ModflowId $modflowModelId): ModflowCalculationWasCreated
    {
        $event = self::occur($calculationId->toString(),[
            'user_id' => $userId->toString(),
            'modflowmodel_id' => $modflowModelId->toString(),
            'soilmodel_id' => $soilModelId->toString()
        ]);

        $event->modflowModelId = $modflowModelId;
        $event->userId = $userId;
        $event->soilModelId = $soilModelId;

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

    public function soilModelId(): SoilModelId
    {
        if ($this->soilModelId === null){
            $this->soilModelId = SoilModelId::fromString($this->payload['soilmodel_id']);
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
