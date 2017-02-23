<?php

namespace Inowas\Modflow\Projection\Calculation;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Modflow\Model\Event\ModflowCalculationResultWasAdded;
use Inowas\Modflow\Model\Event\ModflowCalculationWasCreated;
use Inowas\Modflow\Projection\ProjectionInterface;
use Inowas\Modflow\Projection\Table;

class CalculationResultsProjector implements ProjectionInterface
{

    /** @var Connection $connection */
    protected $connection;

    /** @var Schema $schema */
    protected $schema;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);

        $this->schema = new Schema();
        $table = $this->schema->createTable(Table::CALCULATION_RESULTS);
        $table->addColumn('id', 'integer', array("unsigned" => true, "autoincrement" => true));
        $table->addColumn('calculation_id', 'string', ['length' => 36]);
        $table->addColumn('type', 'string', ['length' => 255]);
        $table->addColumn('totim', 'integer');
        $table->addColumn('layer', 'integer');
        $table->addColumn('data', 'text');
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


    private function executeQueryArray(array $queries)
    {
        foreach ($queries as $query){
            $this->connection->executeQuery($query);
        }
    }

    public function onModflowCalculationWasCreated(ModflowCalculationWasCreated $event)
    {
        echo "--- TEST onModflowCalculationWasCreated CalculationResultsProjector ---\r\n";
    }

    public function onModflowCalculationResultWasAdded(ModflowCalculationResultWasAdded $event)
    {
        echo "--- TEST onModflowCalculationResultWasAdded CalculationResultsProjector ---\r\n";
    }
}
