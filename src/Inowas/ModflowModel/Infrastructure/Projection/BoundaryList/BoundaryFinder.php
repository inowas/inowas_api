<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Infrastructure\Projection\BoundaryList;

use Doctrine\DBAL\Connection;
use Inowas\Common\Boundaries\BoundaryMetadata;
use Inowas\Common\Boundaries\BoundaryName;
use Inowas\Common\Boundaries\BoundaryType;
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

    public function getNumberOfModelBoundariesByType(ModflowId $modelId, BoundaryType $type): int
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT count(*) FROM %s WHERE model_id = :model_id AND type = :type', Table::BOUNDARY_LIST),
            ['model_id' => $modelId->toString(), 'type' => $type->toString()]
        );

        if ($result === false) {
            throw SqlQueryExceptionException::withClassName(__CLASS__, __FUNCTION__);
        }

        return (int)$result['count'];
    }

    public function findConstantHeadBoundaries(ModflowId $modelId): array
    {
        $rows = $this->connection->fetchAll(
            sprintf('SELECT boundary_id as id, name, geometry, affected_layers, metadata FROM %s WHERE model_id = :model_id AND type = :type', Table::BOUNDARY_LIST),
            ['model_id' => $modelId->toString(), 'type' => ConstantHeadBoundary::TYPE]
        );

        $constantHeadBoundaries = array();
        foreach ($rows as $row) {
            $boundaryId = BoundaryId::fromString($row['id']);
            $constantHeadBoundary = ConstantHeadBoundary::createWithParams(
                $boundaryId,
                BoundaryName::fromString($row['name']),
                Geometry::fromArray(json_decode($row['geometry'], true)),
                AffectedLayers::fromArray(json_decode($row['affected_layers'], true)),
                BoundaryMetadata::fromArray(json_decode($row['metadata'], true))
            );

            $results = $this->connection->fetchAll(
                sprintf('SELECT observation_point_id AS id, observation_point_name AS name, observation_point_geometry AS geometry, values as values  FROM %s WHERE model_id = :model_id AND boundary_id = :boundary_id', Table::BOUNDARY_OBSERVATION_POINT_VALUES),
                ['model_id' => $modelId->toString(), 'boundary_id' => $boundaryId->toString()]
            );

            foreach ($results as $result){
                $op = ObservationPoint::fromIdNameAndGeometry(
                    ObservationPointId::fromString($result['id']),
                    ObservationPointName::fromString($result['name']),
                    Geometry::fromArray(json_decode($result['geometry'], true))
                );

                $constantHeadBoundary->addObservationPoint($op);
                foreach (json_decode($result['values']) as $arrayValues){
                    $constantHeadBoundary->addConstantHeadToObservationPoint($op->id(), ConstantHeadDateTimeValue::fromArrayValues($arrayValues));
                }
            }

            $constantHeadBoundaries[] = $constantHeadBoundary;
        }

        return $constantHeadBoundaries;
    }

    public function findGeneralHeadBoundaries(ModflowId $modelId): array
    {
        $rows = $this->connection->fetchAll(
            sprintf('SELECT boundary_id as id, name, geometry, affected_layers, metadata FROM %s WHERE model_id = :model_id AND type = :type', Table::BOUNDARY_LIST),
            ['model_id' => $modelId->toString(), 'type' => GeneralHeadBoundary::TYPE]
        );

        $generalHeadBoundaries = array();
        foreach ($rows as $row) {
            $boundaryId = BoundaryId::fromString($row['id']);
            $generalHeadBoundary = GeneralHeadBoundary::createWithParams(
                $boundaryId,
                BoundaryName::fromString($row['name']),
                Geometry::fromArray(json_decode($row['geometry'], true)),
                AffectedLayers::fromArray(json_decode($row['affected_layers'], true)),
                BoundaryMetadata::fromArray(json_decode($row['metadata'], true))
            );

            $results = $this->connection->fetchAll(
                sprintf('SELECT observation_point_id AS id, observation_point_name AS name, observation_point_geometry AS geometry, values as values  FROM %s WHERE model_id = :model_id AND boundary_id = :boundary_id', Table::BOUNDARY_OBSERVATION_POINT_VALUES),
                ['model_id' => $modelId->toString(), 'boundary_id' => $boundaryId->toString()]
            );

            foreach ($results as $result){
                $op = ObservationPoint::fromIdNameAndGeometry(
                    ObservationPointId::fromString($result['id']),
                    ObservationPointName::fromString($result['name']),
                    Geometry::fromArray(json_decode($result['geometry'], true))
                );

                $generalHeadBoundary->addObservationPoint($op);
                foreach (json_decode($result['values']) as $arrayValues){
                    $generalHeadBoundary->addGeneralHeadValueToObservationPoint($op->id(), GeneralHeadDateTimeValue::fromArrayValues($arrayValues));
                }
            }

            $generalHeadBoundaries[] = $generalHeadBoundary;
        }

        return $generalHeadBoundaries;
    }

    public function findRechargeBoundaries(ModflowId $modelId): array
    {
        $rows = $this->connection->fetchAll(
            sprintf('SELECT boundary_id, name, geometry, affected_layers, metadata FROM %s WHERE model_id = :model_id AND type = :type', Table::BOUNDARY_LIST),
            ['model_id' => $modelId->toString(), 'type' => RechargeBoundary::TYPE]
        );

        $recharges = array();
        foreach ($rows as $row) {
            $boundaryId = BoundaryId::fromString($row['boundary_id']);
            $recharge = RechargeBoundary::createWithParams(
                $boundaryId,
                BoundaryName::fromString($row['name']),
                Geometry::fromArray(json_decode($row['geometry'], true)),
                AffectedLayers::fromArray(json_decode($row['affected_layers'], true)),
                BoundaryMetadata::fromArray(json_decode($row['metadata'], true))
            );

            $result = $this->connection->fetchAssoc(
                sprintf('SELECT values FROM %s WHERE model_id = :model_id AND boundary_id = :boundary_id', Table::BOUNDARY_OBSERVATION_POINT_VALUES),
                ['model_id' => $modelId->toString(), 'boundary_id' => $boundaryId->toString()]
            );

            foreach (json_decode($result['values']) as $arrayValues){
                $recharge->addRecharge(RechargeDateTimeValue::fromArrayValues($arrayValues));
            }

            $recharges[] = $recharge;
        }
        return $recharges;
    }

    public function findRiverBoundaries(ModflowId $modelId): array
    {
        $rows = $this->connection->fetchAll(
            sprintf('SELECT boundary_id as id, name, geometry, affected_layers, metadata FROM %s WHERE model_id = :model_id AND type = :type', Table::BOUNDARY_LIST),
            ['model_id' => $modelId->toString(), 'type' => RiverBoundary::TYPE]
        );

        $rivers = array();
        foreach ($rows as $row) {
            $boundaryId = BoundaryId::fromString($row['id']);
            $river = RiverBoundary::createWithParams(
                $boundaryId,
                BoundaryName::fromString($row['name']),
                Geometry::fromArray(json_decode($row['geometry'], true)),
                AffectedLayers::fromArray(json_decode($row['affected_layers'], true)),
                BoundaryMetadata::fromArray(json_decode($row['metadata'], true))
            );

            $results = $this->connection->fetchAll(
                sprintf('SELECT observation_point_id AS id, observation_point_name AS name, observation_point_geometry AS geometry, values as values  FROM %s WHERE model_id = :model_id AND boundary_id = :boundary_id', Table::BOUNDARY_OBSERVATION_POINT_VALUES),
                ['model_id' => $modelId->toString(), 'boundary_id' => $boundaryId->toString()]
            );

            foreach ($results as $result){
                $op = ObservationPoint::fromIdNameAndGeometry(
                    ObservationPointId::fromString($result['id']),
                    ObservationPointName::fromString($result['name']),
                    Geometry::fromArray(json_decode($result['geometry'], true))
                );

                $river->addObservationPoint($op);
                foreach (json_decode($result['values']) as $arrayValues){
                    $river->addRiverStageToObservationPoint($op->id(), RiverDateTimeValue::fromArrayValues($arrayValues));
                }
            }

            $rivers[] = $river;
        }

        return $rivers;
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
                AffectedLayers::fromArray(json_decode($row['affected_layers'], true)),
                BoundaryMetadata::fromArray(json_decode($row['metadata'], true))
            );

            $result = $this->connection->fetchAssoc(
                sprintf('SELECT values FROM %s WHERE model_id = :model_id AND boundary_id = :boundary_id', Table::BOUNDARY_OBSERVATION_POINT_VALUES),
                ['model_id' => $modelId->toString(), 'boundary_id' => $boundaryId->toString()]
            );

            foreach (json_decode($result['values']) as $arrayValues){
                $well->addPumpingRate(WellDateTimeValue::fromArrayValues($arrayValues));
            }

            $wells[] = $well;
        }

        return $wells;
    }

    public function findBoundariesByModelId(ModflowId $modelId): ?array
    {
        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);

        $result = $this->connection->fetchAll(
            sprintf('SELECT boundary_id as id, name, type, geometry, metadata, affected_layers FROM %s WHERE model_id = :model_id', Table::BOUNDARY_LIST),
            ['model_id' => $modelId->toString()]
        );

        if (false === $result) {
            return null;
        }

        return $result;
    }

    public function getBoundaryDetails(ModflowId $modelId, BoundaryId $boundaryId): ?array
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT boundary_id AS id, name AS name, type AS type, affected_layers, geometry as geometry, metadata as metadata, observation_point_ids as observation_point_ids FROM %s WHERE model_id = :model_id AND boundary_id = :boundary_id', Table::BOUNDARY_LIST),
            ['model_id' => $modelId->toString(), 'boundary_id' => $boundaryId->toString()]
        );

        if ($result === false){
            return null;
        }

        $observationPointIds = json_decode($result['observation_point_ids']);

        if (! is_array($observationPointIds)) {
            return null;
        }

        $observationPoints = [];
        foreach ($observationPointIds as $observationPointId) {

            $observationPointId = ObservationPointId::fromString($observationPointId);
            $opResult = $this->getBoundaryObservationPointDetails($modelId, $boundaryId, $observationPointId);

            if (null === $opResult){
                continue;
            }

            $observationPoints[] = $opResult;
        }

        $result['affected_layers'] = json_decode($result['affected_layers']);
        $result['geometry'] = json_decode($result['geometry'], true);
        $result['metadata'] = json_decode($result['metadata'], true);
        $result['observation_points'] = $observationPoints;
        $result['active_cells'] = $this->findBoundaryActiveCells($modelId, $boundaryId);

        return $result;
    }

    public function getBoundaryName(ModflowId $modelId, BoundaryId $boundaryId): ?BoundaryName
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT name FROM %s WHERE model_id = :model_id AND boundary_id = :boundary_id', Table::BOUNDARY_LIST),
            ['model_id' => $modelId->toString(), 'boundary_id' => $boundaryId->toString()]
        );

        if ($result === false){
            return null;
        }

        return BoundaryName::fromString($result['name']);
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

    public function getBoundaryObservationPointDetails(ModflowId $modelId, BoundaryId $boundaryId, ObservationPointId $observationPointId): ?array
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT observation_point_id as id, observation_point_name as name, observation_point_geometry as geometry, values_description, values FROM %s WHERE model_id = :model_id AND boundary_id = :boundary_id AND observation_point_id = observation_point_id', Table::BOUNDARY_OBSERVATION_POINT_VALUES),
            ['model_id' => $modelId->toString(), 'boundary_id' => $boundaryId->toString(), 'observation_point_id' => $observationPointId->toString()]
        );

        if (null === $result){
            return null;
        }

        $result['geometry'] = json_decode($result['geometry']);
        $result['values_description'] = json_decode($result['values_description']);
        $result['values'] = json_decode($result['values']);
        return $result;
    }

    public function getBoundaryObservationPointName(ModflowId $modelId, BoundaryId $boundaryId, ObservationPointId $observationPointId): ?ObservationPointName
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT observation_point_name as name FROM %s WHERE model_id = :model_id AND boundary_id = :boundary_id AND observation_point_id = observation_point_id', Table::BOUNDARY_OBSERVATION_POINT_VALUES),
            ['model_id' => $modelId->toString(), 'boundary_id' => $boundaryId->toString(), 'observation_point_id' => $observationPointId->toString()]
        );

        if (null === $result){
            return null;
        }

        return ObservationPointName::fromString($result['name']);
    }

    public function getBoundaryObservationPointGeometry(ModflowId $modelId, BoundaryId $boundaryId, ObservationPointId $observationPointId): ?Geometry
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT observation_point_geometry as geometry FROM %s WHERE model_id = :model_id AND boundary_id = :boundary_id AND observation_point_id = observation_point_id', Table::BOUNDARY_OBSERVATION_POINT_VALUES),
            ['model_id' => $modelId->toString(), 'boundary_id' => $boundaryId->toString(), 'observation_point_id' => $observationPointId->toString()]
        );

        if (null === $result){
            return null;
        }

        return Geometry::fromArray(json_decode($result['geometry']));
    }

    public function getBoundaryObservationPointValues(ModflowId $modelId, BoundaryId $boundaryId, ObservationPointId $observationPointId): ?array
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT values_description, values FROM %s WHERE model_id = :model_id AND boundary_id = :boundary_id AND observation_point_id = observation_point_id', Table::BOUNDARY_OBSERVATION_POINT_VALUES),
            ['model_id' => $modelId->toString(), 'boundary_id' => $boundaryId->toString(), 'observation_point_id' => $observationPointId->toString()]
        );

        if (null === $result){
            return null;
        }

        return array(
            'values_description' => json_decode($result['values_description']),
            'values' => json_decode($result['values'])
        );
    }

    public function getBoundaryType(ModflowId $modelId, BoundaryId $boundaryId): ?BoundaryType
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT type FROM %s WHERE model_id = :model_id AND boundary_id = :boundary_id', Table::BOUNDARY_LIST),
            ['model_id' => $modelId->toString(), 'boundary_id' => $boundaryId->toString()]
        );

        if ($result === false){
            return null;
        }

        return BoundaryType::fromString($result['type']);
    }

    public function findStressPeriodDatesById(ModflowId $modelId): array
    {
        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);
        $boundaries = $this->connection->fetchAll(
            sprintf('SELECT values FROM %s WHERE model_id = :model_id', Table::BOUNDARY_OBSERVATION_POINT_VALUES),
            ['model_id' => $modelId->toString()]
        );

        if ($boundaries === false) {
            throw SqlQueryExceptionException::withClassName(__CLASS__, __FUNCTION__);
        }

        $spDates = [];
        foreach ($boundaries as $boundary){
            $dataValues = json_decode($boundary['values']);
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

    public function findAreaActiveCells(ModflowId $modelId): ?ActiveCells
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT active_cells FROM %s WHERE boundary_id =:boundary_id AND model_id = :model_id', Table::BOUNDARY_ACTIVE_CELLS),
            ['model_id' => $modelId->toString(), 'boundary_id' => $modelId->toString()]
        );

        if (null === $result['active_cells']){
            return null;
        }

        return ActiveCells::fromArray(json_decode($result['active_cells'], true));
    }

    public function updateAreaActiveCells(ModflowId $modelId, ActiveCells $activeCells): void
    {
        $boundaryId = BoundaryId::fromString($modelId->toString());
        $this->updateBoundaryActiveCells($modelId, $boundaryId, $activeCells);
    }

    public function findBoundaryActiveCells(ModflowId $modelId, BoundaryId $boundaryId): ?ActiveCells
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT active_cells FROM %s WHERE boundary_id =:boundary_id AND model_id = :model_id', Table::BOUNDARY_ACTIVE_CELLS),
            ['model_id' => $modelId->toString(), 'boundary_id' => $boundaryId->toString()]
        );

        if (null === $result['active_cells']){
            return null;
        }

        return ActiveCells::fromArray(json_decode($result['active_cells'], true));
    }

    public function updateBoundaryActiveCells(ModflowId $modelId, BoundaryId $boundaryId, ActiveCells $activeCells): void
    {
        $this->connection->update(Table::BOUNDARY_ACTIVE_CELLS, array(
            'active_cells' => json_encode($activeCells->toArray())
        ), array(
            'model_id' => $modelId->toString(),
            'boundary_id' => $boundaryId->toString(),
        ));
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
