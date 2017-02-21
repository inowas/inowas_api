<?php

namespace Inowas\Modflow\Projection\BoundaryList;

use Doctrine\DBAL\Connection;
use Inowas\Modflow\Model\ModflowId;
use Inowas\Modflow\Model\UserId;
use Inowas\Modflow\Projection\Table;

class BoundaryFinder
{
    /** @var Connection $connection */
    protected $connection;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
        $this->connection->setFetchMode(\PDO::FETCH_OBJ);
    }

    public function findByUserAndBaseModelId(UserId $userId, ModflowId $baseModelId)
    {
        return $this->connection->fetchAll(
            sprintf('SELECT boundary_id, name, geometry FROM %s WHERE base_model_id = :base_model_id AND user_id = :user_id AND scenario_id = :scenario_id', Table::BOUNDARIES),
            [
                'base_model_id' => $baseModelId->toString(),
                'user_id' => $userId->toString(),
                'scenario_id' => ''
            ]
        );
    }

    public function findByUserAndBaseModelAndScenarioId(UserId $userId, ModflowId $baseModelId, ModflowId $scenarioId)
    {
        return $this->connection->fetchAll(
            sprintf('SELECT boundary_id, name, geometry FROM %s WHERE base_model_id = :base_model_id AND user_id = :user_id AND scenario_id = :scenario_id', Table::BOUNDARIES),
            [
                'base_model_id' => $baseModelId->toString(),
                'user_id' => $userId->toString(),
                'scenario_id' => $scenarioId->toString()
            ]
        );
    }
}
