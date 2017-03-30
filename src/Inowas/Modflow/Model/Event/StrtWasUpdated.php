<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Modflow\Strt;
use Prooph\EventSourcing\AggregateChanged;

class StrtWasUpdated extends AggregateChanged
{
    /** @var ModflowId */
    private $calculationId;

    /** @var  Strt */
    protected $strt;

    public static function to(
        ModflowId $calculationId,
        Strt $strt
    ): StrtWasUpdated
    {
        $event = self::occur($calculationId->toString(),[
            'strt' => $strt->toValue()
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

    public function strt(): Strt
    {
        if ($this->strt === null) {
            $this->strt = Strt::fromValue($this->payload['strt']);
        }

        return $this->strt;
    }
}
