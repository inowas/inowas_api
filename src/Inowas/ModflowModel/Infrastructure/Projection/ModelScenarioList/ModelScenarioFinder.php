<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Infrastructure\Projection\ModelScenarioList;

use Doctrine\DBAL\Connection;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\ModflowModel\Infrastructure\Projection\Table;

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

    public function findBaseModelById(ModflowId $baseModelId)
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

    public function findBoundingBoxByModelId(ModflowId $modelId): ?BoundingBox
    {
        $baseModelId = $this->findBaseModelIdByModelId($modelId);
        return $this->findBoundingBoxByBaseModelId($baseModelId);
    }

    public function findGridSizeByModelId(ModflowId $modelId): ?GridSize
    {
        $baseModelId = $this->findBaseModelIdByModelId($modelId);
        return $this->findGridSizeByBaseModelId($baseModelId);
    }

    private function findBaseModelIdByModelId(ModflowId $modelId): ModflowId
    {
        $result =  $this->connection->fetchAssoc(
            sprintf('SELECT base_model_id FROM %s WHERE base_model_id = :model_id OR scenario_id = :model_id', Table::MODEL_SCENARIO_LIST),
            ['model_id' => $modelId->toString()]
        );

        return ModflowId::fromString($result['base_model_id']);
    }

    private function findGridSizeByBaseModelId(ModflowId $modelId): GridSize
    {
        $result =  $this->connection->fetchAssoc(
            sprintf('SELECT grid_size FROM %s WHERE base_model_id = :model_id AND scenario_id = :scenario_id ORDER BY id', Table::MODEL_SCENARIO_LIST),
            [
                'model_id' => $modelId->toString(),
                'scenario_id' => ''
            ]
        );

        return GridSize::fromArray((array)json_decode($result['grid_size']));
    }

    private function findBoundingBoxByBaseModelId(ModflowId $modelId): BoundingBox
    {
        $result =  $this->connection->fetchAssoc(
            sprintf('SELECT bounding_box FROM %s WHERE base_model_id = :model_id AND scenario_id = :scenario_id ORDER BY id', Table::MODEL_SCENARIO_LIST),
            [
                'model_id' => $modelId->toString(),
                'scenario_id' => ''
            ]
        );

        return BoundingBox::fromArray((array)json_decode($result['bounding_box']));
    }
}
