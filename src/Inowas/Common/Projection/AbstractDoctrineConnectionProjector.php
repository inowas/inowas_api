<?php

declare(strict_types=1);

namespace Inowas\Common\Projection;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Doctrine\DBAL\Schema\Schema;

abstract class AbstractDoctrineConnectionProjector implements ProjectionInterface
{

    /** @var Schema $schema */
    protected $schema;

    /** @var Schema $schema */
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);
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
}
