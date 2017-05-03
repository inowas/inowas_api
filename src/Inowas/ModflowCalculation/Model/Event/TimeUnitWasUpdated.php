<?php

declare(strict_types=1);

namespace Inowas\ModflowCalculation\Model\Event;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Modflow\TimeUnit;
use Prooph\EventSourcing\AggregateChanged;

class TimeUnitWasUpdated extends AggregateChanged
{
    /** @var ModflowId */
    private $calculationId;

    /** @var  TimeUnit */
    protected $timeUnit;

    public static function to(
        ModflowId $calculationId,
        TimeUnit $timeUnit
    ): TimeUnitWasUpdated
    {
        $event = self::occur($calculationId->toString(),[
            'time_unit' => $timeUnit->toInt()
        ]);

        return $event;
    }

    public function calculationId(): ModflowId
    {
        if ($this->calculationId === null){
            $this->calculationId = ModflowId::fromString($this->aggregateId());
        }

        return $this->calculationId;
    }

    public function timeUnit(): TimeUnit
    {
        if ($this->timeUnit === null) {
            $this->timeUnit = TimeUnit::fromInt($this->payload['time_unit']);
        }

        return $this->timeUnit;
    }
}
