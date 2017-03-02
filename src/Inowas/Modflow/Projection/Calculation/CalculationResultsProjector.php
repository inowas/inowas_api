<?php

declare(strict_types=1);

namespace Inowas\Modflow\Projection\Calculation;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Modflow\Model\Event\HeadWasCalculated;
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
        $table->addColumn('filename', 'string', ['length' => 255]);
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

    public function onHeadWasCalculated(HeadWasCalculated $event): void
    {
        $this->connection->insert(Table::CALCULATION_RESULTS, array(
            'calculation_id' => $event->calculationId()->toString(),
            'type' => $event->type()->toString(),
            'totim' => $event->totalTime()->toInteger(),
            'layer' => $event->layer()->toInteger(),
            'filename' => $event->filename()->toString()
        ));
    }
}
