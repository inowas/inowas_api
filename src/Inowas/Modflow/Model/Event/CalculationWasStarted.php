<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Common\Id\ModflowId;
use Prooph\EventSourcing\AggregateChanged;

class CalculationWasStarted extends AggregateChanged
{
    /** @var  ModflowId */
    private $calculationId;

    public static function withId(
        ModflowId $calculationId
    ): CalculationWasStarted
    {
        $event = self::occur($calculationId->toString());

        $event->calculationId = $calculationId;
        return $event;
    }

    public function calculationId(): ModflowId
    {
        if ($this->calculationId === null){
            $this->calculationId = ModflowId::fromString($this->aggregateId());
        }

        return $this->calculationId;
    }
}
