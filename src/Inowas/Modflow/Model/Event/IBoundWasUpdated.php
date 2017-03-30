<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Modflow\IBound;
use Prooph\EventSourcing\AggregateChanged;

class IBoundWasUpdated extends AggregateChanged
{
    /** @var ModflowId */
    private $calculationId;

    /** @var  IBound */
    protected $iBound;

    public static function to(
        ModflowId $calculationId,
        IBound $iBound
    ): IBoundWasUpdated
    {
        $event = self::occur($calculationId->toString(),[
            'ibound' => $iBound->toValue()
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

    public function iBound(): IBound
    {
        if ($this->iBound === null) {
            $this->iBound = IBound::fromValue($this->payload['ibound']);
        }

        return $this->iBound;
    }
}
