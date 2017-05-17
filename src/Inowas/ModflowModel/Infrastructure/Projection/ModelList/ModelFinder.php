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
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\Modelname;
use Inowas\Common\Modflow\ModflowModelDescription;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Common\Soilmodel\SoilmodelId;
use Inowas\ModflowModel\Infrastructure\Projection\Table;

class ModelFinder
{
    /** @var Connection $connection */
    protected $connection;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
        $this->connection->setFetchMode(\PDO::FETCH_OBJ);
    }

    public function getAreaGeometryByModflowModelId(ModflowId $modelId): ?Geometry
    {
        $result =  $this->connection->fetchAssoc(
            sprintf('SELECT area FROM %s WHERE model_id = :model_id', Table::MODEL_DETAILS),
            ['model_id' => $modelId->toString()]
        );

        if ($result === false){
            return null;
        }

        return Geometry::fromJson($result['area']);
    }

    public function getAreaPolygonByModflowModelId(ModflowId $modelId): ?Polygon
    {
        return $this->getAreaGeometryByModflowModelId($modelId)->value();
    }

    public function getBoundingBoxByModflowModelId(ModflowId $modelId): ?BoundingBox
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

    public function getGridSizeByModflowModelId(ModflowId $modelId): ?GridSize
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

    public function getModelNameByModelId(ModflowId $modelId): ?Modelname
    {
        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT name FROM %s WHERE model_id = :model_id', Table::MODEL_DETAILS),
            ['model_id' => $modelId->toString()]
        );

        if ($result === false){
            return null;
        }

        return Modelname::fromString($result['name']);
    }

    public function getModelDescriptionByModelId(ModflowId $modelId): ?ModflowModelDescription
    {
        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT description FROM %s WHERE model_id = :model_id', Table::MODEL_DETAILS),
            ['model_id' => $modelId->toString()]
        );

        if ($result === false){
            return null;
        }

        return ModflowModelDescription::fromString($result['description']);
    }

    public function getSoilmodelIdByModelId(ModflowId $modelId): ?SoilmodelId
    {
        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT soilmodel_id FROM %s WHERE model_id = :model_id', Table::MODEL_DETAILS),
            ['model_id' => $modelId->toString()]
        );

        if ($result === false){
            return null;
        }

        return SoilmodelId::fromString($result['soilmodel_id']);
    }

    public function getLengthUnitByModelId(ModflowId $modelId): ?LengthUnit
    {
        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT length_unit FROM %s WHERE model_id = :model_id', Table::MODEL_DETAILS),
            ['model_id' => $modelId->toString()]
        );

        if ($result === false){
            return null;
        }

        return LengthUnit::fromInt($result['length_unit']);
    }

    public function getTimeUnitByModelId(ModflowId $modelId): ?TimeUnit
    {
        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT time_unit FROM %s WHERE model_id = :model_id', Table::MODEL_DETAILS),
            ['model_id' => $modelId->toString()]
        );

        if ($result === false){
            return null;
        }

        return TimeUnit::fromInt($result['time_unit']);
    }

    public function userHasWriteAccessToModel(UserId $userId, ModflowId $modelId): bool
    {
        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT count(*) FROM %s WHERE model_id = :model_id AND user_id = :user_id', Table::MODEL_DETAILS),
            ['model_id' => $modelId->toString(), 'user_id' => $userId->toString()]
        );

        if ($result['count'] > 0){
            return true;
        }

        return false;
    }

    public function getModelDetailsByModelId(ModflowId $modelId): array
    {
        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT model_id AS id, user_id, soilmodel_id, user_name, name, description, area as area_geometry, length_unit, time_unit, grid_size, bounding_box, created_at, public FROM %s WHERE model_id = :model_id', Table::MODEL_DETAILS),
            ['model_id' => $modelId->toString()]
        );

        if ($result === false){
            return [];
        }

        $result['area_geometry'] = json_decode($result['area_geometry'], true);
        $result['grid_size'] = json_decode($result['grid_size'], true);
        $result['bounding_box'] = json_decode($result['bounding_box'], true);

        return $result;
    }

    public function findModelsByBaseUserId(UserId $userId): array
    {
        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);
        $rows = $this->connection->fetchAll(
            sprintf('SELECT model_id AS id, user_id, soilmodel_id, user_name, name, description, area as area_geometry, grid_size, bounding_box, created_at, public FROM %s WHERE user_id = :user_id', Table::MODEL_DETAILS),
            ['user_id' => $userId->toString()]
        );

        foreach ($rows as $key => $row){
            $rows[$key]['area_geometry'] = json_decode($row['area_geometry'], true);
            $rows[$key]['grid_size'] = json_decode($row['grid_size'], true);
            $rows[$key]['bounding_box'] = json_decode($row['bounding_box'], true);
        }

        return $rows;
    }

    public function findPublicModels(): array
    {
        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);
        $rows = $this->connection->fetchAll(
            sprintf('SELECT model_id AS id, user_id, soilmodel_id, user_name, name, description, area as area_geometry, grid_size, bounding_box, created_at, public FROM %s WHERE public = :public', Table::MODEL_DETAILS),
            ['public' => true]
        );

        foreach ($rows as $key => $row){
            $rows[$key]['area_geometry'] = json_decode($row['area_geometry'], true);
            $rows[$key]['grid_size'] = json_decode($row['grid_size'], true);
            $rows[$key]['bounding_box'] = json_decode($row['bounding_box'], true);
        }

        return $rows;
    }

    public function findAll(): array
    {
        return $this->connection->fetchAll(
            sprintf('SELECT * FROM %s', Table::MODEL_DETAILS)
        );
    }
}
