<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Modflow\Model\Budget;
use Inowas\Modflow\Model\ModflowId;
use Inowas\Modflow\Model\TotalTime;
use Prooph\EventSourcing\AggregateChanged;

class BudgetWasCalculated extends AggregateChanged
{
    /** @var  ModflowId */
    private $calculationId;

    /** @var  TotalTime */
    protected $totalTime;

    /** @var  Budget */
    protected $budget;

    public static function to(
        ModflowId $calculationId,
        TotalTime $totalTime,
        Budget $budget
    ): BudgetWasCalculated
    {
        $event = self::occur($calculationId->toString(),[
            'total_time' => $totalTime->toInteger(),
            'budget' => $budget->toArray()
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

    public function totalTime(): TotalTime
    {
        if ($this->totalTime === null) {
            $this->totalTime = TotalTime::fromInt($this->payload['total_time']);
        }

        return $this->totalTime;
    }

    public function budget(): Budget
    {
        if ($this->budget === null) {
            $this->budget = Budget::fromArray($this->payload['budget']);
        }

        return $this->budget;
    }
}
