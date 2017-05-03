<?php

declare(strict_types=1);

namespace Inowas\ModflowCalculation\Model\Event;

use Inowas\Common\Id\ModflowId;
use Inowas\ModflowCalculation\Model\ModflowCalculationResponse;
use Prooph\EventSourcing\AggregateChanged;

class CalculationWasFinished extends AggregateChanged
{
    /** @var  ModflowId */
    private $calculationId;

    /** @var  \Inowas\ModflowCalculation\Model\ModflowCalculationResponse */
    protected $response;

    public static function withIdAndResponse(
        ModflowId $calculationId,
        ModflowCalculationResponse $response
    ): CalculationWasFinished
    {
        $event = self::occur($calculationId->toString(),[
            'response' => $response->toArray()
        ]);

        $event->calculationId = $calculationId;
        $event->response = $response;
        return $event;
    }

    public function calculationId(): ModflowId
    {
        if ($this->calculationId === null){
            $this->calculationId = ModflowId::fromString($this->aggregateId());
        }

        return $this->calculationId;
    }

    public function response(): ModflowCalculationResponse
    {
        if ($this->response === null){
            $this->response = ModflowCalculationResponse::fromArray($this->payload['response']);
        }

        return $this->response;
    }
}
