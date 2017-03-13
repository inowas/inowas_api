<?php

declare(strict_types=1);

namespace Inowas\Modflow\Projection\ModelScenarioList;

use Doctrine\DBAL\Connection;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
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
            sprintf('SELECT * FROM %s WHERE base_model_id = :base_model_id AND user_id = :user_id ORDER BY id', Table::MODEL_SCENARIO_LIST),
            [
                'base_model_id' => $baseModelId->toString(),
                'user_id' => $userId->toString()
            ]
        );
    }

    public function findBaseModelByUserAndId(UserId $userId, ModflowId $baseModelId)
    {
        return $this->connection->fetchAll(
            sprintf('SELECT base_model_id as model_id, name, description, area, grid_size, bounding_box FROM %s WHERE base_model_id = :base_model_id AND user_id = :user_id c ORDER BY id', Table::MODEL_SCENARIO_LIST),
            [
                'base_model_id' => $baseModelId->toString(),
                'scenario_id' => '',
                'user_id' => $userId->toString()
            ]
        );
    }

    public function findScenariosByUserAndBaseModelId(UserId $userId, ModflowId $baseModelId)
    {
        return $this->connection->fetchAll(
            sprintf('SELECT scenario_id as model_id, name, description FROM %s WHERE base_model_id = :base_model_id AND user_id = :user_id AND NOT scenario_id = :scenario_id ORDER BY id', Table::MODEL_SCENARIO_LIST),
            [
                'base_model_id' => $baseModelId->toString(),
                'scenario_id' => '',
                'user_id' => $userId->toString()
            ]
        );
    }

    public function findByBaseModelId(ModflowId $baseModelId)
    {
        return $this->connection->fetchAll(
            sprintf('SELECT * FROM %s WHERE base_model_id = :base_model_id AND scenario_id = :scenario_id', Table::MODEL_SCENARIO_LIST),
            [
                'base_model_id' => $baseModelId->toString(),
                'scenario_id' => ''
            ]
        );
    }

    public function findScenariosByBaseModelId(ModflowId $baseModelId)
    {
        return $this->connection->fetchAll(
            sprintf('SELECT scenario_id as model_id, name, description FROM %s WHERE base_model_id = :base_model_id AND NOT scenario_id = :scenario_id ORDER BY id', Table::MODEL_SCENARIO_LIST),
            [
                'base_model_id' => $baseModelId->toString(),
                'scenario_id' => ''
            ]
        );
    }
}
