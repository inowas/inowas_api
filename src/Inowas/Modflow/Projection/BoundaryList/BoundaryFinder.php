<?php

declare(strict_types=1);

namespace Inowas\Modflow\Projection\BoundaryList;

use Doctrine\DBAL\Connection;
use Inowas\Common\Boundaries\BoundaryName;
use Inowas\Common\Boundaries\ConstantHeadBoundary;
use Inowas\Common\Boundaries\ConstantHeadDateTimeValue;
use Inowas\Common\Boundaries\ObservationPoint;
use Inowas\Common\Boundaries\ObservationPointName;
use Inowas\Common\Boundaries\PumpingRates;
use Inowas\Common\Boundaries\RiverBoundary;
use Inowas\Common\Boundaries\RiverDateTimeValue;
use Inowas\Common\Boundaries\WellBoundary;
use Inowas\Common\Boundaries\WellDateTimeValue;
use Inowas\Common\Boundaries\WellType;
use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Grid\LayerNumber;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\ObservationPointId;
use Inowas\Modflow\Model\Exception\SqlQueryExceptionException;
use Inowas\Modflow\Projection\Table;

class BoundaryFinder
{
    /** @var Connection $connection */
    protected $connection;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
        $this->connection->setFetchMode(\PDO::FETCH_OBJ);
    }

    public function countModelBoundaries(ModflowId $modelId, string $type): int
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT count(*) FROM %s WHERE model_id = :model_id AND type = :type', Table::BOUNDARIES),
            [
                'model_id' => $modelId->toString(),
                'type' => $type
            ]
        );

        if ($result === false) {
            throw SqlQueryExceptionException::withClassName(__CLASS__, __FUNCTION__);
        }

        return (int)$result['count'];
    }

    public function findWells(ModflowId $modelId): array
    {
        $rows = $this->connection->fetchAll(
            sprintf('SELECT boundary_id, name, geometry, metadata, active_cells FROM %s WHERE model_id = :model_id AND type = :type', Table::BOUNDARIES),
            ['model_id' => $modelId->toString(), 'type' => WellBoundary::TYPE]
        );

        $wells = array();
        foreach ($rows as $row) {
            $well = WellBoundary::createWithParams(
                BoundaryId::fromString($row['boundary_id']),
                BoundaryName::fromString($row['name']),
                Geometry::fromJson($row['geometry']),
                WellType::fromString(json_decode($row['metadata'])->well_type),
                LayerNumber::fromInteger(json_decode($row['metadata'])->layer)
            )->setActiveCells(ActiveCells::fromArray((array)json_decode($row['active_cells'])));

            $wells[] = $well;
        }

        /** @var WellBoundary $well */
        foreach ($wells as $wellKey => $well){
            $result = $this->connection->fetchAssoc(
                sprintf('SELECT data FROM %s WHERE observation_point_id = :observation_point_id', Table::BOUNDARY_VALUES),
                ['observation_point_id' => $well->boundaryId()->toString()]
            );

            $data = json_decode($result['data']);
            foreach ($data as $dateTimeValue) {
                $wells[$wellKey] = $well->addPumpingRate(WellDateTimeValue::fromArray((array)$dateTimeValue));
            }
        }

        return $wells;
    }

    public function findRivers(ModflowId $modelId): array
    {
        $rows = $this->connection->fetchAll(
            sprintf('SELECT boundary_id, name, geometry, metadata, active_cells FROM %s WHERE model_id = :model_id AND type = :type', Table::BOUNDARIES),
            ['model_id' => $modelId->toString(), 'type' => RiverBoundary::TYPE]
        );

        $rivers = array();
        foreach ($rows as $row) {
            $river = RiverBoundary::createWithParams(
                BoundaryId::fromString($row['boundary_id']),
                BoundaryName::fromString($row['name']),
                Geometry::fromJson($row['geometry'])
            )->setActiveCells(ActiveCells::fromArray((array)json_decode($row['active_cells'])));

            $rivers[] = $river;
        }

        /** @var RiverBoundary $river */
        foreach ($rivers as $riverKey => $river){

            $observationPoints = $this->connection->fetchAll(
                sprintf('SELECT observation_point_id, observation_point_name, observation_point_geometry, data FROM %s WHERE boundary_id = :boundary_id', Table::BOUNDARY_VALUES),
                ['boundary_id' => $river->boundaryId()->toString()]
            );

            foreach ($observationPoints as $observationPoint) {
                $op = ObservationPoint::fromIdNameAndGeometry(
                    ObservationPointId::fromString($observationPoint['observation_point_id']),
                    ObservationPointName::fromString($observationPoint['observation_point_name']),
                    Geometry::fromJson($observationPoint['observation_point_geometry'])
                );

                $river = $river->addObservationPoint($op);

                $data = json_decode($observationPoint['data']);
                foreach ($data as $dateTimeValue) {
                    $rivers[$riverKey] = $river->addRiverStageToObservationPoint($op->id(), RiverDateTimeValue::fromArray((array)$dateTimeValue));
                }
            }
        }

        return $rivers;
    }

    public function findChdBoundaries(ModflowId $modelId): array
    {
        $rows = $this->connection->fetchAll(
            sprintf('SELECT boundary_id, name, geometry, metadata, active_cells FROM %s WHERE model_id = :model_id AND type = :type', Table::BOUNDARIES),
            ['model_id' => $modelId->toString(), 'type' => ConstantHeadBoundary::TYPE]
        );

        $constantHeadBoundaries = array();
        foreach ($rows as $row) {
            $constantHeadBoundary = ConstantHeadBoundary::createWithParams(
                BoundaryId::fromString($row['boundary_id']),
                BoundaryName::fromString($row['name']),
                Geometry::fromJson($row['geometry'])
            )->setActiveCells(ActiveCells::fromArray((array)json_decode($row['active_cells'])));

            $constantHeadBoundaries[] = $constantHeadBoundary;
        }

        /** @var ConstantHeadBoundary $constantHeadBoundary */
        foreach ($constantHeadBoundaries as $chdKey => $constantHeadBoundary){

            $observationPoints = $this->connection->fetchAll(
                sprintf('SELECT observation_point_id, observation_point_name, observation_point_geometry, data FROM %s WHERE boundary_id = :boundary_id', Table::BOUNDARY_VALUES),
                ['boundary_id' => $constantHeadBoundary->boundaryId()->toString()]
            );

            foreach ($observationPoints as $observationPoint) {
                $op = ObservationPoint::fromIdNameAndGeometry(
                    ObservationPointId::fromString($observationPoint['observation_point_id']),
                    ObservationPointName::fromString($observationPoint['observation_point_name']),
                    Geometry::fromJson($observationPoint['observation_point_geometry'])
                );

                $constantHeadBoundary = $constantHeadBoundary->addObservationPoint($op);

                $data = json_decode($observationPoint['data']);
                foreach ($data as $dateTimeValue) {
                    $constantHeadBoundaries[$chdKey] = $constantHeadBoundary->addConstantHeadToObservationPoint($op->id(), ConstantHeadDateTimeValue::fromArray((array)$dateTimeValue));
                }
            }
        }

        return $constantHeadBoundaries;
    }

    public function findByModelId(ModflowId $modelId)
    {
        return $this->connection->fetchAll(
            sprintf('SELECT boundary_id, type, name, geometry, metadata FROM %s WHERE model_id = :model_id', Table::BOUNDARIES),
            ['model_id' => $modelId->toString()]
        );
    }

    public function findBoundaryById(ModflowId $modelId, BoundaryId $boundaryId)
    {
        return $this->connection->fetchAssoc(
            sprintf('SELECT * FROM %s WHERE model_id = :model_id AND boundary_id = :boundary_id', Table::BOUNDARIES),
            [
                'model_id' => $modelId->toString(),
                'boundary_id' => $boundaryId->toString()
            ]
        );
    }

    public function findStressPeriodDatesById(ModflowId $modelId): array
    {
        $boundaries = $this->connection->fetchAll(
            sprintf('SELECT data FROM %s WHERE model_id = :model_id', Table::BOUNDARY_VALUES),
            ['model_id' => $modelId->toString()]
        );

        if ($boundaries === false) {
            throw SqlQueryExceptionException::withClassName(__CLASS__, __FUNCTION__);
        }

        $spDates = [];
        foreach ($boundaries as $boundary){
            $dataValues = \json_decode($boundary['data']);
            foreach ($dataValues as $dataValue){
                $dateTimeAtom = DateTime::fromDateTime(new \DateTime($dataValue->date_time))->toAtom();
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
            sprintf('SELECT active_cells FROM %s WHERE type =:type AND model_id = :model_id', Table::BOUNDARIES),
            [
                'model_id' => $modelId->toString(),
                'type' => 'area'
            ]
        );

        return ActiveCells::fromArray((array)json_decode($result['active_cells']));
    }

    public function findBoundaryActiveCells(ModflowId $modelId, BoundaryId $boundaryId): ActiveCells
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT active_cells FROM %s WHERE boundary_id =:boundary_id AND model_id = :model_id', Table::BOUNDARIES),
            [
                'model_id' => $modelId->toString(),
                'boundary_id' => $boundaryId->toString()
            ]
        );

        return ActiveCells::fromArray((array)json_decode($result['active_cells']));
    }
}
