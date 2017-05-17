<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Infrastructure\Projection\BoundaryList;

use Doctrine\DBAL\Connection;
use Inowas\Common\Boundaries\BoundaryName;
use Inowas\Common\Boundaries\ConstantHeadBoundary;
use Inowas\Common\Boundaries\ConstantHeadDateTimeValue;
use Inowas\Common\Boundaries\GeneralHeadBoundary;
use Inowas\Common\Boundaries\GeneralHeadDateTimeValue;
use Inowas\Common\Boundaries\ObservationPoint;
use Inowas\Common\Boundaries\ObservationPointName;
use Inowas\Common\Boundaries\RechargeBoundary;
use Inowas\Common\Boundaries\RechargeDateTimeValue;
use Inowas\Common\Boundaries\RiverBoundary;
use Inowas\Common\Boundaries\RiverDateTimeValue;
use Inowas\Common\Boundaries\WellBoundary;
use Inowas\Common\Boundaries\WellDateTimeValue;
use Inowas\Common\Boundaries\WellType;
use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Grid\AffectedLayers;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\ObservationPointId;
use Inowas\ModflowModel\Model\Exception\SqlQueryExceptionException;
use Inowas\ModflowModel\Infrastructure\Projection\Table;

class BoundaryFinder
{
    /** @var Connection $connection */
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->connection->setFetchMode(\PDO::FETCH_OBJ);
    }

    public function getTotalNumberOfModelBoundaries(ModflowId $modelId): int
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT count(*) FROM %s WHERE model_id = :model_id', Table::BOUNDARY_LIST),
            ['model_id' => $modelId->toString()]
        );

        if ($result === false) {
            throw SqlQueryExceptionException::withClassName(__CLASS__, __FUNCTION__);
        }

        return (int)$result['count'];

    }

    public function getNumberOfModelBoundariesByType(ModflowId $modelId, string $type): int
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT count(*) FROM %s WHERE model_id = :model_id AND type = :type', Table::BOUNDARY_LIST),
            ['model_id' => $modelId->toString(), 'type' => $type]
        );

        if ($result === false) {
            throw SqlQueryExceptionException::withClassName(__CLASS__, __FUNCTION__);
        }

        return (int)$result['count'];
    }

    public function findRechargeBoundaries(ModflowId $modelId): array
    {
        $rows = $this->connection->fetchAll(
            sprintf('SELECT boundary_id, name, geometry FROM %s WHERE model_id = :model_id AND type = :type', Table::BOUNDARY_LIST),
            ['model_id' => $modelId->toString(), 'type' => RechargeBoundary::TYPE]
        );

        $recharges = array();
        foreach ($rows as $row) {
            $boundaryId = BoundaryId::fromString($row['boundary_id']);
            $recharge = RechargeBoundary::createWithParams(
                $boundaryId,
                BoundaryName::fromString($row['name']),
                Geometry::fromArray(json_decode($row['geometry'], true))
            );

            $result = $this->connection->fetchAssoc(
                sprintf('SELECT data FROM %s WHERE model_id = :model_id AND boundary_id = :boundary_id', Table::BOUNDARY_OBSERVATION_POINTS),
                ['model_id' => $modelId->toString(), 'boundary_id' => $boundaryId->toString()]
            );

            foreach (json_decode($result['data']) as $arrayValues){
                $recharge->addRecharge(RechargeDateTimeValue::fromArrayValues($arrayValues));
            }

            $result = $this->connection->fetchAssoc(
                sprintf('SELECT active_cells FROM %s WHERE model_id = :model_id AND boundary_id = :boundary_id', Table::BOUNDARY_ACTIVE_CELLS),
                ['model_id' => $modelId->toString(), 'boundary_id' => $boundaryId->toString()]
            );

            $activeCells = ActiveCells::fromArray(json_decode($result['active_cells'], true));
            $recharge = $recharge->setActiveCells($activeCells);
            $recharges[] = $recharge;
        }
        return $recharges;
    }

    public function findWellBoundaries(ModflowId $modelId): array
    {
        $rows = $this->connection->fetchAll(
            sprintf('SELECT boundary_id as id, name, geometry, metadata, affected_layers FROM %s WHERE model_id = :model_id AND type = :type', Table::BOUNDARY_LIST),
            ['model_id' => $modelId->toString(), 'type' => WellBoundary::TYPE]
        );

        $wells = array();
        foreach ($rows as $row) {
            $boundaryId = BoundaryId::fromString($row['id']);
            $well = WellBoundary::createWithParams(
                $boundaryId,
                BoundaryName::fromString($row['name']),
                Geometry::fromArray(json_decode($row['geometry'], true)),
                WellType::fromString(json_decode($row['metadata'], true)['well_type']),
                AffectedLayers::fromArray(json_decode($row['affected_layers'], true))
            );

            $result = $this->connection->fetchAssoc(
                sprintf('SELECT data FROM %s WHERE model_id = :model_id AND boundary_id = :boundary_id', Table::BOUNDARY_OBSERVATION_POINTS),
                ['model_id' => $modelId->toString(), 'boundary_id' => $boundaryId->toString()]
            );

            foreach (json_decode($result['data']) as $arrayValues){
                $well->addPumpingRate(WellDateTimeValue::fromArrayValues($arrayValues));
            }

            $result = $this->connection->fetchAssoc(
                sprintf('SELECT active_cells FROM %s WHERE model_id = :model_id AND boundary_id = :boundary_id', Table::BOUNDARY_ACTIVE_CELLS),
                ['model_id' => $modelId->toString(), 'boundary_id' => $boundaryId->toString()]
            );

            $activeCells = ActiveCells::fromArray(json_decode($result['active_cells'], true));
            $well = $well->setActiveCells($activeCells);
            $wells[] = $well;
        }

        return $wells;
    }

    public function findRiverBoundaries(ModflowId $modelId): array
    {
        $rows = $this->connection->fetchAll(
            sprintf('SELECT boundary_id as id, name, geometry FROM %s WHERE model_id = :model_id AND type = :type', Table::BOUNDARY_LIST),
            ['model_id' => $modelId->toString(), 'type' => RiverBoundary::TYPE]
        );

        $rivers = array();
        foreach ($rows as $row) {
            $boundaryId = BoundaryId::fromString($row['id']);
            $river = RiverBoundary::createWithParams(
                $boundaryId,
                BoundaryName::fromString($row['name']),
                Geometry::fromArray(json_decode($row['geometry'], true))
            );

            $results = $this->connection->fetchAll(
                sprintf('SELECT observation_point_id AS id, observation_point_name AS name, observation_point_geometry AS geometry, data as data  FROM %s WHERE model_id = :model_id AND boundary_id = :boundary_id', Table::BOUNDARY_OBSERVATION_POINTS),
                ['model_id' => $modelId->toString(), 'boundary_id' => $boundaryId->toString()]
            );

            foreach ($results as $result){
                $op = ObservationPoint::fromIdNameAndGeometry(
                    ObservationPointId::fromString($result['id']),
                    ObservationPointName::fromString($result['name']),
                    Geometry::fromArray(json_decode($result['geometry'], true))
                );

                $river->addObservationPoint($op);
                foreach (json_decode($result['data']) as $arrayValues){
                    $river->addRiverStageToObservationPoint($op->id(), RiverDateTimeValue::fromArrayValues($arrayValues));
                }
            }

            $result = $this->connection->fetchAssoc(
                sprintf('SELECT active_cells FROM %s WHERE model_id = :model_id AND boundary_id = :boundary_id', Table::BOUNDARY_ACTIVE_CELLS),
                ['model_id' => $modelId->toString(), 'boundary_id' => $boundaryId->toString()]
            );

            $activeCells = ActiveCells::fromArray(json_decode($result['active_cells'], true));
            $river = $river->setActiveCells($activeCells);

            $rivers[] = $river;
        }

        return $rivers;
    }

    public function findConstantHeadBoundaries(ModflowId $modelId): array
    {
        $rows = $this->connection->fetchAll(
            sprintf('SELECT boundary_id as id, name, geometry, affected_layers FROM %s WHERE model_id = :model_id AND type = :type', Table::BOUNDARY_LIST),
            ['model_id' => $modelId->toString(), 'type' => ConstantHeadBoundary::TYPE]
        );

        $constantHeadBoundaries = array();
        foreach ($rows as $row) {
            $boundaryId = BoundaryId::fromString($row['id']);
            $constantHeadBoundary = ConstantHeadBoundary::createWithParams(
                $boundaryId,
                BoundaryName::fromString($row['name']),
                Geometry::fromArray(json_decode($row['geometry'], true)),
                AffectedLayers::fromArray(json_decode($row['affected_layers'], true))
            );

            $results = $this->connection->fetchAll(
                sprintf('SELECT observation_point_id AS id, observation_point_name AS name, observation_point_geometry AS geometry, data as data  FROM %s WHERE model_id = :model_id AND boundary_id = :boundary_id', Table::BOUNDARY_OBSERVATION_POINTS),
                ['model_id' => $modelId->toString(), 'boundary_id' => $boundaryId->toString()]
            );

            foreach ($results as $result){
                $op = ObservationPoint::fromIdNameAndGeometry(
                    ObservationPointId::fromString($result['id']),
                    ObservationPointName::fromString($result['name']),
                    Geometry::fromArray(json_decode($result['geometry'], true))
                );

                $constantHeadBoundary->addObservationPoint($op);
                foreach (json_decode($result['data']) as $arrayValues){
                    $constantHeadBoundary->addConstantHeadToObservationPoint($op->id(), ConstantHeadDateTimeValue::fromArrayValues($arrayValues));
                }
            }

            $result = $this->connection->fetchAssoc(
                sprintf('SELECT active_cells FROM %s WHERE model_id = :model_id AND boundary_id = :boundary_id', Table::BOUNDARY_ACTIVE_CELLS),
                ['model_id' => $modelId->toString(), 'boundary_id' => $boundaryId->toString()]
            );

            $activeCells = ActiveCells::fromArray(json_decode($result['active_cells'], true));
            $constantHeadBoundary = $constantHeadBoundary->setActiveCells($activeCells);
            $constantHeadBoundaries[] = $constantHeadBoundary;
        }

        return $constantHeadBoundaries;
    }

    public function findGeneralHeadBoundaries(ModflowId $modelId): array
    {
        $rows = $this->connection->fetchAll(
            sprintf('SELECT boundary_id as id, name, geometry, affected_layers FROM %s WHERE model_id = :model_id AND type = :type', Table::BOUNDARY_LIST),
            ['model_id' => $modelId->toString(), 'type' => GeneralHeadBoundary::TYPE]
        );

        $generalHeadBoundaries = array();
        foreach ($rows as $row) {
            $boundaryId = BoundaryId::fromString($row['id']);
            $generalHeadBoundary = GeneralHeadBoundary::createWithParams(
                $boundaryId,
                BoundaryName::fromString($row['name']),
                Geometry::fromArray(json_decode($row['geometry'], true)),
                AffectedLayers::fromArray(json_decode($row['affected_layers'], true))
            );

            $results = $this->connection->fetchAll(
                sprintf('SELECT observation_point_id AS id, observation_point_name AS name, observation_point_geometry AS geometry, data as data  FROM %s WHERE model_id = :model_id AND boundary_id = :boundary_id', Table::BOUNDARY_OBSERVATION_POINTS),
                ['model_id' => $modelId->toString(), 'boundary_id' => $boundaryId->toString()]
            );

            foreach ($results as $result){
                $op = ObservationPoint::fromIdNameAndGeometry(
                    ObservationPointId::fromString($result['id']),
                    ObservationPointName::fromString($result['name']),
                    Geometry::fromArray(json_decode($result['geometry'], true))
                );

                $generalHeadBoundary->addObservationPoint($op);
                foreach (json_decode($result['data']) as $arrayValues){
                    $generalHeadBoundary->addGeneralHeadValueToObservationPoint($op->id(), GeneralHeadDateTimeValue::fromArrayValues($arrayValues));
                }
            }

            $result = $this->connection->fetchAssoc(
                sprintf('SELECT active_cells FROM %s WHERE model_id = :model_id AND boundary_id = :boundary_id', Table::BOUNDARY_ACTIVE_CELLS),
                ['model_id' => $modelId->toString(), 'boundary_id' => $boundaryId->toString()]
            );

            $activeCells = ActiveCells::fromArray(json_decode($result['active_cells'], true));
            $generalHeadBoundary = $generalHeadBoundary->setActiveCells($activeCells);
            $generalHeadBoundaries[] = $generalHeadBoundary;
        }

        return $generalHeadBoundaries;
    }

    public function findByModelId(ModflowId $modelId): array
    {
        return $this->connection->fetchAll(
            sprintf('SELECT boundary_id, name, type, geometry, metadata FROM %s WHERE model_id = :model_id', Table::BOUNDARY_LIST),
            ['model_id' => $modelId->toString()]
        );
    }

    public function getBoundaryDetails(ModflowId $modelId, BoundaryId $boundaryId): ?array
    {
        $row = $this->connection->fetchAssoc(
            sprintf('SELECT boundary_id AS id, name AS name, type AS type, geometry as geometry, metadata as metadata, observation_point_ids as observation_point_ids FROM %s WHERE model_id = :model_id AND boundary_id = :boundary_id', Table::BOUNDARY_LIST),
            ['model_id' => $modelId->toString(), 'boundary_id' => $boundaryId->toString()]
        );

        return $row;
    }

    public function findStressPeriodDatesById(ModflowId $modelId): array
    {
        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);

        $boundaries = $this->connection->fetchAll(
            sprintf('SELECT data FROM %s WHERE model_id = :model_id', Table::BOUNDARY_OBSERVATION_POINTS),
            ['model_id' => $modelId->toString()]
        );

        if ($boundaries === false) {
            throw SqlQueryExceptionException::withClassName(__CLASS__, __FUNCTION__);
        }

        $spDates = [];
        foreach ($boundaries as $boundary){
            $dataValues = json_decode($boundary['data']);
            foreach ($dataValues as $dataValue){
                $dateTimeAtom = DateTime::fromDateTime(new \DateTime($dataValue[0]))->toAtom();
                if (! in_array($dateTimeAtom, $spDates)) {
                    $spDates[] = DateTime::fromAtom($dateTimeAtom);
                }
            }
        }

        sort($spDates);
        return $spDates;
    }

    public function findAreaActiveCells(ModflowId $modelId): ActiveCells
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT active_cells FROM %s WHERE boundary_id =:boundary_id AND model_id = :model_id', Table::BOUNDARY_ACTIVE_CELLS),
            ['model_id' => $modelId->toString(), 'boundary_id' => $modelId->toString()]
        );

        return ActiveCells::fromArray((array)json_decode($result['active_cells']));
    }

    public function findBoundaryActiveCells(ModflowId $modelId, BoundaryId $boundaryId): ActiveCells
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT active_cells FROM %s WHERE boundary_id =:boundary_id AND model_id = :model_id', Table::BOUNDARY_ACTIVE_CELLS),
            ['model_id' => $modelId->toString(), 'boundary_id' => $boundaryId->toString()]
        );

        return ActiveCells::fromArray(json_decode($result['active_cells'], true));
    }

    public function getBoundaryGeometry(ModflowId $modelId, BoundaryId $boundaryId): ?Geometry
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT geometry FROM %s WHERE boundary_id =:boundary_id AND model_id = :model_id', Table::BOUNDARY_LIST),
            ['model_id' => $modelId->toString(), 'boundary_id' => $boundaryId->toString()]
        );

        if ($result === false){
            return null;
        }

        return Geometry::fromArray(json_decode($result['geometry'], true));
    }

    public function getAffectedLayersByModelAndBoundary(ModflowId $modelId, BoundaryId $boundaryId): AffectedLayers
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT affected_layers FROM %s WHERE model_id = :model_id AND boundary_id = :boundary_id', Table::BOUNDARY_LIST),
            ['model_id' => $modelId->toString(), 'boundary_id' => $boundaryId->toString()]
        );

        return AffectedLayers::fromArray(json_decode($result['affected_layers'], true));
    }

    public function getBoundaryIdsByName(ModflowId $modflowId, BoundaryName $boundaryName): array
    {
        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);
        $rows = $this->connection->fetchAll(
            sprintf('SELECT boundary_id FROM %s WHERE name =:boundary_name AND model_id = :model_id', Table::BOUNDARY_LIST),
            ['model_id' => $modflowId->toString(), 'boundary_name' => $boundaryName->toString()]
        );

        $result = [];
        foreach ($rows as $row){
            $result[] = BoundaryId::fromString($row['boundary_id']);
        }

        return $result;
    }
}
