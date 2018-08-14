<?php

declare(strict_types=1);

namespace Inowas\Common\Projection;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Doctrine\DBAL\Schema\Schema;
use Prooph\Common\Messaging\DomainEvent;

abstract class AbstractDoctrineConnectionProjector implements ProjectionInterface
{
    /** @var Schema[] */
    protected $schemas = [];

    /** @var Connection $connection */
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function createTables(): void
    {
        /** @var Schema $schema */
        foreach ($this->schemas as $schema) {
            $this->createTable($schema);
        }
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function dropTables(): void
    {
        /** @var Schema $schema */
        foreach ($this->schemas as $schema) {
            $this->dropTable($schema);
        }
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function truncateTables(): void
    {
        /** @var Schema $schema */
        foreach ($this->schemas as $schema) {
            $this->dropTable($schema);
            $this->createTable($schema);
        }
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function reset(): void
    {
        $this->truncateTables();
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\DBALException
     */
    private function createTable(Schema $schema): void
    {
        $queryArray = $schema->toSql($this->connection->getDatabasePlatform());
        $this->executeQueryArray($queryArray);
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\DBALException
     */
    private function dropTable(Schema $schema): void
    {
        try {
            $queryArray = $schema->toDropSql($this->connection->getDatabasePlatform());
            $this->executeQueryArray($queryArray);
        } catch (TableNotFoundException $e) {
        }
    }

    /**
     * @param array $queries
     * @throws \Doctrine\DBAL\DBALException
     */
    private function executeQueryArray(array $queries): void
    {
        foreach ($queries as $query) {
            $this->connection->executeQuery($query);
        }
    }

    protected function addSchema(Schema $schema): void
    {
        $this->schemas[] = $schema;
    }

    public function onEvent(DomainEvent $e): void
    {
        $handler = $this->determineEventMethodFor($e);
        if (! method_exists($this, $handler)) {
            throw new \RuntimeException(sprintf(
                'Missing event method %s for projector %s',
                $handler,
                \get_class($this)
            ));
        }
        $this->{$handler}($e);
    }

    protected function determineEventMethodFor(DomainEvent $e): string
    {
        return 'on' . implode(\array_slice(explode('\\', \get_class($e)), -1));
    }
}
