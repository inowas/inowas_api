<?php

namespace Inowas\Soilmodel\Projection\SoilmodelList;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Soilmodel\Model\Event\SoilmodelWasCreated;
use Inowas\Soilmodel\Projection\Table;

class SoilmodelListProjector
{
    /** @var Connection $connection */
    protected $connection;

    /** @var Schema $schema */
    protected $schema;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;

        $this->schema = new Schema();
        $table = $this->schema->createTable(Table::SOILMODEL_LIST);
        $table->addColumn('id', 'integer', array("unsigned" => true, "autoincrement" => true));
        $table->addColumn('user_id', 'string', ['length' => 36]);
        $table->addColumn('soilmodel_id', 'string', ['length' => 36]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('description', 'string', ['length' => 255]);
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
        } catch (TableNotFoundException $e) {
        }
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
        foreach ($queries as $query) {
            $this->connection->executeQuery($query);
        }
    }

    public function onSoilmodelWasCreated(SoilmodelWasCreated $event): void
    {
        $this->connection->insert(Table::SOILMODEL_LIST, array(
            'user_id' => $event->userId()->toString(),
            'soilmodel_id' => $event->soilmodelId()->toString(),
            'name' => '',
            'description' => ''
        ));
    }
}
