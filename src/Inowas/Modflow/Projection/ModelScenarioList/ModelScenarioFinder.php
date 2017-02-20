<?php

namespace Inowas\Modflow\Projection\ModelScenarioList;

use Doctrine\DBAL\Connection;
use Inowas\Modflow\Model\ModflowId;
use Inowas\Modflow\Model\UserId;
use Inowas\Modflow\Projection\Table;

class ModelScenarioFinder
{
    /** @var Connection $connection */
    protected $connection;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
        $this->connection->setFetchMode(\PDO::FETCH_OBJ);
    }

    public function findAll()
    {
        return $this->connection->fetchAll(sprintf('SELECT * FROM %s', Table::MODEL_SCENARIO_LIST));
    }

    public function findByUserAndBaseModelId(UserId $userId, ModflowId $baseModelId)
    {
        return $this->connection->fetchAll(
            sprintf('SELECT * FROM %s WHERE base_model_id = :base_model_id AND user_id = :user_id', Table::MODEL_SCENARIO_LIST),
            [
                'base_model_id' => $baseModelId->toString(),
                'user_id' => $userId->toString()
            ]
        );
    }

    public function findByBaseModelId(ModflowId $baseModelId)
    {
        return $this->connection->fetchAll(
            sprintf('SELECT * FROM %s WHERE base_model_id = :base_model_id', Table::MODEL_SCENARIO_LIST),
            ['base_model_id' => $baseModelId->toString()]
        );
    }
}
