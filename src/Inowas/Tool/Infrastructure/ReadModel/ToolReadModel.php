<?php

namespace Inowas\Tool\Infrastructure\ReadModel;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManager;
use Inowas\AppBundle\Model\User;
use Inowas\Common\Id\UserId;
use Inowas\Tool\Infrastructure\Projection\Table;
use Prooph\EventStore\Projection\AbstractReadModel;

class ToolReadModel extends AbstractReadModel
{
    /**
     * Connection
     *
     * @var Connection
     */
    private $connection;

    /** @var Schema */
    private $schema;


    /** @var  EntityManager */
    private $entityManager;

    public function __construct(EntityManager $entityManager, Connection $connection)
    {

        $this->entityManager = $entityManager;
        $this->connection = $connection;

        $this->schema = new Schema();
        $table = $this->schema->createTable(Table::TOOL_LIST_READ_MODEL);
        $table->addColumn('id', 'string', ['length' => 36]);
        $table->addColumn('name', 'string', ['length' => 255, 'default' => '']);
        $table->addColumn('description', 'string', ['length' => 255, 'default' => '']);
        $table->addColumn('project', 'string', ['length' => 255, 'default' => '']);
        $table->addColumn('application', 'string', ['length' => 255, 'default' => '']);
        $table->addColumn('tool', 'string', ['length' => 255, 'default' => '']);
        $table->addColumn('created_at', 'string', ['length' => 255, 'notnull' => false]);
        $table->addColumn('user_id', 'string', ['length' => 36]);
        $table->addColumn('user_name', 'string', ['length' => 255]);
        $table->addColumn('public', 'smallint', ['default' => 1]);
        $table->addColumn('data', 'text', ['default' => '[]']);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['tool']);
        $table->addIndex(['user_id']);
    }

    private function executeQueryArray(array $queries): void
    {
        foreach ($queries as $query) {
            $this->connection->executeQuery($query);
        }
    }

    private function createTable(): void
    {
        $queries = $this->schema->toSql($this->connection->getDatabasePlatform());
        foreach ($queries as $query) {
            $this->connection->executeQuery($query);
        }
    }

    public function dropTable(): void
    {
        try {
            $queryArray = $this->schema->toDropSql($this->connection->getDatabasePlatform());
            $this->executeQueryArray($queryArray);
        } catch (TableNotFoundException $e) {
            print($e->getMessage());
            print($e->getTraceAsString());
        }
    }

    /**
     *
     */
    public function init(): void
    {
        $this->createTable();
    }

    /**
     * @return bool
     */
    public function isInitialized(): bool
    {
        return $this->connection->getSchemaManager()->tablesExist(array(Table::TOOL_LIST_READ_MODEL));
    }

    /**
     *
     */
    public function reset(): void
    {
        $this->dropTable();
        $this->createTable();
    }

    /**
     *
     */
    public function delete(): void
    {
        $this->dropTable();
    }

    public function insert(array $data): void
    {
        $this->connection->insert(Table::TOOL_LIST_READ_MODEL, $data);
    }

    public function update(array $identifier, array $data): void
    {
        $this->connection->update(Table::TOOL_LIST_READ_MODEL, $data, $identifier);
    }


    public function getUserNameByUserId(UserId $id): string
    {
        $username = '';
        $user = $this->entityManager->getRepository('InowasAppBundle:User')->findOneBy(array('id' => $id->toString()));
        if ($user instanceof User) {
            $username = $user->getName();
        }

        return $username;
    }
}
