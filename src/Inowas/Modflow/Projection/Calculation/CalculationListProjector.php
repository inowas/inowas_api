<?php

namespace Inowas\Modflow\Projection\Calculation;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Modflow\Model\Event\ModflowCalculationWasCreated;
use Inowas\Modflow\Projection\ProjectionInterface;
use Inowas\Modflow\Projection\Table;

class CalculationListProjector implements ProjectionInterface
{

    /** @var Connection $connection */
    protected $connection;

    /** @var Schema $schema */
    protected $schema;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);

        $this->schema = new Schema();
        $table = $this->schema->createTable(Table::CALCULATION_LIST);
        $table->addColumn('calculation_id', 'string', ['length' => 36]);
        $table->addColumn('modflow_model_id', 'string', ['length' => 36]);
        $table->addColumn('user_id', 'string', ['length' => 36]);
        $table->addColumn('soilmodel_id', 'string', ['length' => 36]);
        $table->addColumn('grid_size', 'text');
        $table->setPrimaryKey(['calculation_id']);
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
        $this->connection->insert(Table::CALCULATION_LIST, array(
            'calculation_id' => $event->calculationId()->toString(),
            'modflow_model_id' => $event->modflowModelId()->toString(),
            'user_id' => $event->userId()->toString(),
            'soilmodel_id' => $event->soilModelId()->toString(),
            'grid_size' => json_encode($event->gridSize())
        ));
    }
}
