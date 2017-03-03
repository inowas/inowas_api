<?php

declare(strict_types=1);

namespace Inowas\Modflow\Projection\Calculation;

use Doctrine\DBAL\Connection;
use Inowas\Common\Calculation\BudgetType;
use Inowas\Common\DateTime\TotalTime;
use Inowas\Common\Id\ModflowId;
use Inowas\Modflow\Projection\Table;

class CalculationBudgetFinder
{
    /** @var Connection $connection */
    protected $connection;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);
    }

    public function findBudget(ModflowId $calculationId, TotalTime $totalTime, BudgetType $budgetType)
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT budget from %s WHERE calculation_id = :calculation_id AND totim = :totim AND budget_type = :budget_type', Table::CALCULATION_BUDGETS),
            [
                'calculation_id' => $calculationId->toString(),
                'totim' => $totalTime->toInteger(),
                'budget_type' => $budgetType->toString()
            ]
        );

        if ($result == false){
            return [];
        }

        return json_decode($result['budget']);
    }
}
