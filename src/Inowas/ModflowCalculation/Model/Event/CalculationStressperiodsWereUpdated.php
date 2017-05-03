<?php

declare(strict_types=1);

namespace Inowas\ModflowCalculation\Model\Event;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\StressPeriods;
use Prooph\EventSourcing\AggregateChanged;

class CalculationStressperiodsWereUpdated extends AggregateChanged
{
    /** @var  ModflowId */
    private $calculationId;

    /** @var  UserId */
    private $userId;

    /** @var  StressPeriods */
    private $stressPeriods;

    public static function withProps(
        UserId $userId,
        ModflowId $calculationId,
        StressPeriods $stressPeriods
    ): CalculationStressperiodsWereUpdated
    {
        $event = self::occur($calculationId->toString(),[
            'user_id' => $userId->toString(),
            'stressperiods' => serialize($stressPeriods)
        ]);

        $event->userId = $userId;
        $event->calculationId = $calculationId;
        $event->stressPeriods = $stressPeriods;
        return $event;
    }

    public function calculationId(): ModflowId
    {
        if ($this->calculationId === null){
            $this->calculationId = ModflowId::fromString($this->aggregateId());
        }

        return $this->calculationId;
    }

    public function userId(): UserId
    {
        if ($this->userId === null){
            $this->userId = UserId::fromString($this->payload['user_id']);
        }

        return $this->userId;
    }

    public function stressPeriods(): StressPeriods
    {
        if ($this->stressPeriods === null){
            $this->stressPeriods = unserialize($this->payload['stressperiods']);
        }
        return $this->stressPeriods;
    }
}
