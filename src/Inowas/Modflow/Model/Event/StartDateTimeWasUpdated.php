<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Id\ModflowId;
use Prooph\EventSourcing\AggregateChanged;

class StartDateTimeWasUpdated extends AggregateChanged
{
    /** @var ModflowId */
    private $calculationId;

    /** @var  DateTime */
    protected $start;

    public static function to(
        ModflowId $calculationId,
        DateTime $start
    ): StartDateTimeWasUpdated
    {
        $event = self::occur($calculationId->toString(),[
            'start' => $start->toAtom()
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

    public function start(): DateTime
    {
        if ($this->start === null) {
            $this->start = DateTime::fromAtom($this->payload['start']);
        }

        return $this->start;
    }
}
