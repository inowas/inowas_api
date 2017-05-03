<?php

declare(strict_types=1);

namespace Inowas\ModflowCalculation\Model\Event;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Modflow\LengthUnit;
use Prooph\EventSourcing\AggregateChanged;

class LengthUnitWasUpdated extends AggregateChanged
{
    /** @var ModflowId */
    private $calculationId;

    /** @var  LengthUnit */
    protected $lengthUnit;

    public static function to(
        ModflowId $calculationId,
        LengthUnit $lengthUnit
    ): LengthUnitWasUpdated
    {
        $event = self::occur($calculationId->toString(),[
            'length_unit' => $lengthUnit->toInt()
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

    public function lengthUnit(): LengthUnit
    {
        if ($this->lengthUnit === null) {
            $this->lengthUnit = LengthUnit::fromInt($this->payload['length_unit']);
        }

        return $this->lengthUnit;
    }
}
