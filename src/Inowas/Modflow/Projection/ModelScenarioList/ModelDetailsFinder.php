<?php

declare(strict_types=1);

namespace Inowas\Modflow\Projection\ModelScenarioList;

use Doctrine\DBAL\Connection;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Modflow\Projection\Table;

class ModelDetailsFinder
{
    /** @var Connection $connection */
    protected $connection;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
        $this->connection->setFetchMode(\PDO::FETCH_OBJ);
    }

    public function findByBaseModelId(ModflowId $modelId): array
    {
        return $this->connection->fetchAssoc(
            sprintf('SELECT * FROM %s WHERE model_id = :model_id', Table::MODEL_DETAILS),
            ['model_id' => $modelId->toString()]
        );
    }

    public function findByBaseUserId(UserId $userId): array
    {
        return $this->connection->fetchAll(
            sprintf('SELECT * FROM %s WHERE user_id = :user_id', Table::MODEL_DETAILS),
            ['user_id' => $userId->toString()]
        );
    }

    public function findPublic(): array
    {
        return $this->connection->fetchAll(
            sprintf('SELECT * FROM %s WHERE public = :public', Table::MODEL_DETAILS),
            ['public' => true]
        );
    }
}
