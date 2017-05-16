<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Infrastructure\Projection\ModelList;

use Doctrine\DBAL\Connection;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Geometry\Polygon;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\ModflowModel\Infrastructure\Projection\Table;

class ModelFinder
{
    /** @var Connection $connection */
    protected $connection;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
        $this->connection->setFetchMode(\PDO::FETCH_OBJ);
    }

    public function findByModelId(ModflowId $modelId): array
    {
        return $this->connection->fetchAssoc(
            sprintf('SELECT * FROM %s WHERE model_id = :model_id', Table::MODEL_DETAILS),
            ['model_id' => $modelId->toString()]
        );
    }

    public function findAreaGeometryByModflowModelId(ModflowId $modelId): Polygon
    {
        $result =  $this->connection->fetchAssoc(
            sprintf('SELECT area FROM %s WHERE model_id = :model_id', Table::MODEL_DETAILS),
            ['model_id' => $modelId->toString()]
        );

        if ($result === false){
            return null;
        }

        return Geometry::fromJson($result['area'])->value();
    }

    public function getBoundingBoxByModflowModelId(ModflowId $modelId): BoundingBox
    {
        $result =  $this->connection->fetchAssoc(
            sprintf('SELECT bounding_box FROM %s WHERE model_id = :model_id', Table::MODEL_DETAILS),
            ['model_id' => $modelId->toString()]
        );

        if ($result === false){
            return null;
        }

        return BoundingBox::fromArray((array)json_decode($result['bounding_box']));
    }

    public function getGridSizeByModflowModelId(ModflowId $modelId): GridSize
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT grid_size FROM %s WHERE model_id = :model_id', Table::MODEL_DETAILS),
            ['model_id' => $modelId->toString()]
        );

        if ($result === false){
            return null;
        }

        return GridSize::fromArray((array)json_decode($result['grid_size']));
    }

    public function findByBaseUserId(UserId $userId): array
    {
        $result = $this->connection->fetchAll(
            sprintf('SELECT * FROM %s WHERE user_id = :user_id', Table::MODEL_DETAILS),
            ['user_id' => $userId->toString()]
        );

        return $result;
    }

    public function findPublic(): array
    {
        return $this->connection->fetchAll(
            sprintf('SELECT * FROM %s WHERE public = :public', Table::MODEL_DETAILS),
            ['public' => true]
        );
    }

    public function findAll(): array
    {
        return $this->connection->fetchAll(
            sprintf('SELECT * FROM %s', Table::MODEL_DETAILS)
        );
    }
}
