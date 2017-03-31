<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Prooph\EventSourcing\AggregateChanged;

class CalculationWasUpdated extends AggregateChanged
{
    /** @var  ModflowId */
    private $calculationId;

    /** @var  ModflowId */
    private $modflowModelId;

    /** @var UserId */
    private $userId;

    private $payload;


    public static function fromModelWithProps(
        UserId $userId,
        ModflowId $calculationId,
        ModflowId $modflowModelId,
        $payload
    ): CalculationWasUpdated
    {
        $event = self::occur($calculationId->toString(),[
            'user_id' => $userId->toString(),
            'modflowmodel_id' => $modflowModelId->toString(),
            'payload' => serialize($payload)
        ]);

        $event->calculationId = $calculationId;
        $event->modflowModelId = $modflowModelId;
        $event->userId = $userId;
        $event->payload = $payload;
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

    public function payload()
    {
        if ($this->payload === null){
            $this->payload = unserialize($this->payload['payload']);
        }

        return $this->payload;
    }

    public function userId(): UserId{
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }
}
