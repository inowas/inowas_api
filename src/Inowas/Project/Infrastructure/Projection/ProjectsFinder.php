<?php

declare(strict_types=1);

namespace Inowas\Project\Infrastructure\Projection;

use Doctrine\DBAL\Connection;
use Inowas\Common\Id\UserId;
use Inowas\Project\Model\ApplicationType;
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

    public function getApplicationTypeById(ProjectId $id): ?ApplicationType
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT application FROM %s WHERE id = :id', Table::PROJECT_LIST),
            ['id' => $id->toString()]
        );

        if ($result === false) {
            return null;
        }

        return ApplicationType::fromString($result['application']);
    }

    public function isPublic(ProjectId $projectId): bool
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT public FROM %s WHERE id = :id', Table::PROJECT_LIST),
            ['id' => $projectId->toString()]
        );

        if ($result === false) {
            return false;
        }

        return $result['public'];
    }

    public function isProjectOwner(ProjectId $projectId, UserId $userId): bool
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT count(user_id) FROM %s WHERE id = :id AND user_id = :user_id', Table::PROJECT_LIST),
            ['id' => $projectId->toString(), 'user_id' => $userId->toString()]
        );

        return $result['count'] > 0;
    }

    public function canBeClonedByUser(ProjectId $projectId, UserId $userId): bool
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT count(user_id) FROM %s WHERE (id = :id AND user_id = :user_id) OR (id = :id AND public = :public)', Table::PROJECT_LIST),
            ['id' => $projectId->toString(), 'user_id' => $userId->toString(), 'public' => true]
        );

        return $result['count'] > 0;
    }
}
