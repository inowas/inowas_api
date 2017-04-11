<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Common\Id\ModflowId;
use Inowas\Soilmodel\Interpolation\FlopyCalculationResponse;
use Prooph\EventSourcing\AggregateChanged;

class CalculationWasFinished extends AggregateChanged
{
    /** @var  ModflowId */
    private $calculationId;

    /** @var  FlopyCalculationResponse */
    protected $response;

    public static function withIdAndResponse(
        ModflowId $calculationId,
        FlopyCalculationResponse $response
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

    public function response(): FlopyCalculationResponse
    {
        if ($this->response === null){
            $this->response = FlopyCalculationResponse::fromArray($this->payload['response']);
        }

        return $this->response;
    }
}
