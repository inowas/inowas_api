<?php

declare(strict_types=1);

namespace Inowas\Modflow\Projection\Calculation;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\Modflow\Model\Event\BudgetWasCalculated;
use Inowas\Modflow\Projection\Table;

class CalculationBudgetsProjector extends AbstractDoctrineConnectionProjector
{
    public function __construct(Connection $connection) {

        parent::__construct($connection);

        $this->schema = new Schema();
        $table = $this->schema->createTable(Table::CALCULATION_BUDGETS);
        $table->addColumn('id', 'integer', array("unsigned" => true, "autoincrement" => true));
        $table->addColumn('calculation_id', 'string', ['length' => 36]);
        $table->addColumn('totim', 'integer');
        $table->addColumn('budget_type', 'string', ['length' => 36]);
        $table->addColumn('budget', 'text');
        $table->setPrimaryKey(['id']);
    }

    public function onBudgetWasCalculated(BudgetWasCalculated $event): void
    {
        $this->connection->insert(Table::CALCULATION_BUDGETS, array(
            'calculation_id' => $event->calculationId()->toString(),
            'totim' => $event->totalTime()->toInteger(),
            'budget' => json_encode($event->budget()->toArray()),
            'budget_type' => $event->type()->toString()
        ));
    }
}
