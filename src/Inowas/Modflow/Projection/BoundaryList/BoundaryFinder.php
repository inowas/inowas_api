<?php

declare(strict_types=1);

namespace Inowas\Modflow\Projection\BoundaryList;

use Doctrine\DBAL\Connection;
use Inowas\Common\Boundaries\BoundaryName;
use Inowas\Common\Boundaries\PumpingRates;
use Inowas\Common\Boundaries\WellBoundary;
use Inowas\Common\Boundaries\WellType;
use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Grid\LayerNumber;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ModflowId;
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

    public function findWells(ModflowId $modelId): array
    {
        $rows = $this->connection->fetchAll(
            sprintf('SELECT * FROM %s WHERE model_id = :model_id AND type = :type', Table::BOUNDARIES),
            [
                'model_id' => $modelId->toString(),
                'type' => WellBoundary::TYPE
            ]
        );

        $wells = array();
        foreach ($rows as $row) {
            $well = WellBoundary::createWithAllParams(
                BoundaryId::fromString($row['boundary_id']),
                BoundaryName::fromString($row['name']),
                Geometry::fromJson($row['geometry']),
                WellType::fromString(json_decode($row['metadata'])->well_type),
                LayerNumber::fromInteger(json_decode($row['metadata'])->layer),
                PumpingRates::fromJson($row['data'])
            )->setActiveCells(ActiveCells::fromArray((array)json_decode($row['active_cells'])));

            $wells[] = $well;
        }

        return $wells;
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
            sprintf('SELECT boundary_id, type, data FROM %s WHERE model_id = :model_id', Table::BOUNDARIES),
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
}
