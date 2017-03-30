<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Id\ModflowId;
use Prooph\EventSourcing\AggregateChanged;

class EndDateTimeWasUpdated extends AggregateChanged
{
    /** @var ModflowId */
    private $calculationId;

    /** @var  DateTime */
    protected $end;

    public static function to(
        ModflowId $calculationId,
        DateTime $start
    ): EndDateTimeWasUpdated
    {
        $event = self::occur($calculationId->toString(),[
            'end' => $start->toAtom()
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

    public function end(): DateTime
    {
        if ($this->end === null) {
            $this->end = DateTime::fromAtom($this->payload['end']);
        }

        return $this->end;
    }
}
