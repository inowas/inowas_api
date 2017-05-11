<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Infrastructure\Projection\BoundaryList;

use Doctrine\DBAL\Connection;
use Inowas\Common\Boundaries\BoundaryName;
use Inowas\Common\Boundaries\ConstantHeadBoundary;
use Inowas\Common\Boundaries\GeneralHeadBoundary;
use Inowas\Common\Boundaries\RechargeBoundary;
use Inowas\Common\Boundaries\RiverBoundary;
use Inowas\Common\Boundaries\WellBoundary;
use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ModflowId;
use Inowas\ModflowModel\Model\Exception\SqlQueryExceptionException;
use Inowas\ModflowModel\Infrastructure\Projection\Table;

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

    public function findBoundaries(ModflowId $modelId): array
    {
        $rows = $this->connection->fetchAll(
            sprintf('SELECT boundary FROM %s WHERE model_id = :model_id', Table::BOUNDARIES),
            ['model_id' => $modelId->toString()]
        );

        $boundaries = array();
        foreach ($rows as $row) {
            $boundary = unserialize(base64_decode($row['boundary']));
            $boundaries[] = $boundary;
        }

        return $boundaries;
    }

    public function findRecharge(ModflowId $modelId): array
    {
        $rows = $this->connection->fetchAll(
            sprintf('SELECT boundary FROM %s WHERE model_id = :model_id AND type = :type', Table::BOUNDARIES),
            ['model_id' => $modelId->toString(), 'type' => RechargeBoundary::TYPE]
        );

        $recharges = array();
        foreach ($rows as $row) {
            $recharge = unserialize(base64_decode($row['boundary']));
            $recharges[] = $recharge;
        }

        return $recharges;
    }

    public function findWells(ModflowId $modelId): array
    {
        $rows = $this->connection->fetchAll(
            sprintf('SELECT boundary_id, boundary FROM %s WHERE model_id = :model_id AND type = :type', Table::BOUNDARIES),
            ['model_id' => $modelId->toString(), 'type' => WellBoundary::TYPE]
        );

        $wells = array();
        foreach ($rows as $row) {
            $well = unserialize(base64_decode($row['boundary']));
            $wells[] = $well;
        }

        return $wells;
    }

    public function findRivers(ModflowId $modelId): array
    {
        $rows = $this->connection->fetchAll(
            sprintf('SELECT boundary_id, boundary FROM %s WHERE model_id = :model_id AND type = :type', Table::BOUNDARIES),
            ['model_id' => $modelId->toString(), 'type' => RiverBoundary::TYPE]
        );

        $rivers = array();
        foreach ($rows as $row) {
            $river = unserialize(base64_decode($row['boundary']));
            $rivers[] = $river;
        }

        return $rivers;
    }

    public function findChdBoundaries(ModflowId $modelId): array
    {
        $rows = $this->connection->fetchAll(
            sprintf('SELECT boundary_id, boundary FROM %s WHERE model_id = :model_id AND type = :type', Table::BOUNDARIES),
            ['model_id' => $modelId->toString(), 'type' => ConstantHeadBoundary::TYPE]
        );

        $constantHeadBoundaries = array();
        foreach ($rows as $row) {
            $constantHeadBoundary = unserialize(base64_decode($row['boundary']));
            $constantHeadBoundaries[] = $constantHeadBoundary;
        }

        return $constantHeadBoundaries;
    }

    public function findGhbBoundaries(ModflowId $modelId): array
    {
        $rows = $this->connection->fetchAll(
            sprintf('SELECT boundary_id, boundary FROM %s WHERE model_id = :model_id AND type = :type', Table::BOUNDARIES),
            ['model_id' => $modelId->toString(), 'type' => GeneralHeadBoundary::TYPE]
        );

        $generalHeadBoundaries = array();
        foreach ($rows as $row) {
            $generalHeadBoundary = unserialize(base64_decode($row['boundary']));
            $generalHeadBoundaries[] = $generalHeadBoundary;
        }

        return $generalHeadBoundaries;
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
        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);

        $boundaries = $this->connection->fetchAll(
            sprintf('SELECT data FROM %s WHERE model_id = :model_id', Table::BOUNDARY_VALUES),
            ['model_id' => $modelId->toString()]
        );

        if ($boundaries === false) {
            throw SqlQueryExceptionException::withClassName(__CLASS__, __FUNCTION__);
        }

        $spDates = [];
        foreach ($boundaries as $boundary){
            $dataValues = json_decode($boundary['data']);
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

    public function getBoundaryIdsByName(ModflowId $modflowId, BoundaryName $boundaryName): array
    {
        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);
        $rows = $this->connection->fetchAll(
            sprintf('SELECT boundary_id FROM %s WHERE name =:boundary_name AND model_id = :model_id', Table::BOUNDARIES),
            [
                'model_id' => $modflowId->toString(),
                'boundary_name' => $boundaryName->toString()
            ]
        );

        $result = [];
        foreach ($rows as $row){
            $result[] = BoundaryId::fromString($row['boundary_id']);
        }

        return $result;
    }
}
