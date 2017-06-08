<?php

declare(strict_types=1);

namespace Inowas\Project\Infrastructure\Projection;

use Doctrine\DBAL\Connection;
use Inowas\Common\Id\UserId;
use Inowas\Project\Model\ProjectId;

class ProjectsFinder
{
    /** @var Connection $connection */
    protected $connection;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);
    }

    public function findPublic(): array
    {
        $results = $this->connection->fetchAll(
            sprintf('SELECT id, name, description, project, application, created_at, user_id, user_name, created_at, public FROM %s WHERE public = true', Table::PROJECT_LIST)
        );

        if ($results === false) {
            $results = [];
        }

        return $results;
    }

    public function findByUserId(UserId $userId): array
    {
        $results = $this->connection->fetchAll(
            sprintf('SELECT id, name, description, project, application, created_at, user_id, user_name, created_at, public FROM %s WHERE user_id = :user_id', Table::PROJECT_LIST),
            ['user_id' => $userId->toString()]
        );

        if ($results === false) {
            $results = [];
        }

        return $results;
    }

    public function findById(ProjectId $id): ?array
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT id, name, description, project, application, created_at, user_id, user_name, created_at, public FROM %s WHERE id = :id', Table::PROJECT_LIST),
            ['id' => $id->toString()]
        );

        if ($result === false) {
            return null;
        }

        return $result;
    }
}
