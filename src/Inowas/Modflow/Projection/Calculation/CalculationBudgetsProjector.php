<?php

declare(strict_types=1);

namespace Inowas\Modflow\Projection\Calculation;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Modflow\Model\Event\BudgetWasCalculated;
use Inowas\Modflow\Model\Event\HeadWasCalculated;
use Inowas\Modflow\Projection\ProjectionInterface;
use Inowas\Modflow\Projection\Table;

class CalculationBudgetsProjector implements ProjectionInterface
{

    /** @var Connection $connection */
    protected $connection;

    /** @var Schema $schema */
    protected $schema;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);

        $this->schema = new Schema();
        $table = $this->schema->createTable(Table::CALCULATION_BUDGETS);
        $table->addColumn('id', 'integer', array("unsigned" => true, "autoincrement" => true));
        $table->addColumn('calculation_id', 'string', ['length' => 36]);
        $table->addColumn('totim', 'integer');
        $table->addColumn('budget', 'text');
        $table->setPrimaryKey(['id']);
    }

    public function createTable(): void
    {
        $queryArray = $this->schema->toSql($this->connection->getDatabasePlatform());
        $this->executeQueryArray($queryArray);

    }

    public function dropTable(): void
    {
        try {
            $queryArray = $this->schema->toDropSql($this->connection->getDatabasePlatform());
            $this->executeQueryArray($queryArray);
        } catch (TableNotFoundException $e) {}
    }

    public function truncateTable(): void
    {
        $this->dropTable();
        $this->createTable();
    }

    public function reset(): void
    {
        $this->truncateTable();
    }

    private function executeQueryArray(array $queries): void
    {
        foreach ($queries as $query){
            $this->connection->executeQuery($query);
        }
    }

    public function onBudgetWasCalculated(BudgetWasCalculated $event): void
    {
        $this->connection->insert(Table::CALCULATION_BUDGETS, array(
            'calculation_id' => $event->calculationId()->toString(),
            'totim' => $event->totalTime()->toInteger(),
            'budget' => json_encode($event->budget()->toArray())
        ));
    }
}
