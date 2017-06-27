<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Event;

use Inowas\Common\Id\CalculationId;
use Inowas\Common\Id\ModflowId;
use Inowas\ModflowModel\Model\AMQP\CalculationResponse;
use Prooph\EventSourcing\AggregateChanged;

/** @noinspection LongInheritanceChainInspection */
class CalculationWasFinished extends AggregateChanged
{
    /** @var  ModflowId */
    private $modelId;

    /** @var  CalculationResponse */
    protected $response;

    public static function withResponse(
        ModflowId $modelId,
        CalculationResponse $response
    ): CalculationWasFinished
    {
        $event = self::occur($modelId->toString(),
            ['response' => $response->toArray()]
        );

        $event->modelId = $modelId;
        $event->response = $response;
        return $event;
    }


    public function modelId(): ModflowId
    {
        if ($this->modelId === null){
            $this->modelId = ModflowId::fromString($this->aggregateId());
        }

        return $this->modelId;
    }

    public function calculationId(): CalculationId
    {
        return $this->response()->calculationId();
    }

    public function response(): CalculationResponse
    {
        if ($this->response === null){
            $this->response = CalculationResponse::fromArray($this->payload['response']);
        }

        return $this->response;
    }
}