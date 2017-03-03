<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Event;

use Inowas\Common\Calculation\Budget;
use Inowas\Common\Calculation\BudgetType;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\DateTime\TotalTime;
use Prooph\EventSourcing\AggregateChanged;

class BudgetWasCalculated extends AggregateChanged
{
    /** @var ModflowId */
    private $calculationId;

    /** @var  TotalTime */
    protected $totalTime;

    /** @var  Budget */
    protected $budget;

    /** @var  BudgetType */
    protected $budgetType;

    public static function to(
        ModflowId $calculationId,
        TotalTime $totalTime,
        Budget $budget,
        BudgetType $budgetType
    ): BudgetWasCalculated
    {
        $event = self::occur($calculationId->toString(),[
            'total_time' => $totalTime->toInteger(),
            'budget' => $budget->toArray(),
            'type' => $budgetType->toString()
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

    public function type(): BudgetType
    {
        if ($this->budgetType === null) {
            $this->budgetType = BudgetType::fromString($this->payload['type']);
        }

        return $this->budgetType;
    }
}
