<?php

declare(strict_types=1);

namespace Inowas\ModflowBoundary\Infrastructure\Projection\BoundaryList;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\ModflowBoundary\Model\Event\BoundaryObservationPointWasAdded;
use Inowas\ModflowBoundary\Model\Event\BoundaryObservationPointWasUpdated;
use Inowas\ModflowBoundary\Model\Event\BoundaryWasCloned;
use Inowas\ModflowBoundary\Model\Event\BoundaryWasRemoved;
use Inowas\ModflowModel\Infrastructure\Projection\Table;

class BoundaryObservationPointsProjector extends AbstractDoctrineConnectionProjector
{

    public function __construct(Connection $connection) {

        parent::__construct($connection);

        $schema = new Schema();
        $table = $schema->createTable(Table::BOUNDARY_OBSERVATION_POINT_VALUES);
        $table->addColumn('boundary_id', 'string', ['length' => 36]);
        $table->addColumn('boundary_type', 'string', ['length' => 10]);
        $table->addColumn('observation_point_id', 'string', ['length' => 36]);
        $table->addColumn('observation_point_name', 'string', ['length' => 255]);
        $table->addColumn('observation_point_geometry', 'text', ['notnull' => false]);
        $table->addColumn('values_description', 'text', ['notnull' => false]);
        $table->addColumn('values', 'text', ['notnull' => false, 'default' => '[]']);
        $table->addIndex(array('boundary_id'));
        $this->addSchema($schema);
    }

    public function onBoundaryObservationPointWasAdded(BoundaryObservationPointWasAdded $event): void
    {
        $observationPoint = $event->observationPoint();
        $observationPointGeometry = $observationPoint->geometry();

        $this->connection->insert(Table::BOUNDARY_OBSERVATION_POINT_VALUES, array(
            'boundary_id' => $event->boundaryId()->toString(),
            'boundary_type' => $observationPoint->type()->toString(),
            'observation_point_id' => $observationPoint->id()->toString(),
            'observation_point_name' => $observationPoint->name()->toString(),
            'observation_point_geometry' => json_encode(Geometry::fromPoint($observationPointGeometry)),
            'values_description' => json_encode($observationPoint->dateTimeValuesDescription()),
            'values' => json_encode($observationPoint->dateTimeValues())
        ));
    }

    public function onBoundaryObservationPointWasUpdated(BoundaryObservationPointWasUpdated $event): void
    {
        $observationPoint = $event->observationPoint();
        $observationPointGeometry = $observationPoint->geometry();

        $this->connection->update(Table::BOUNDARY_OBSERVATION_POINT_VALUES,
            array(
                'observation_point_name' => $observationPoint->name()->toString(),
                'observation_point_geometry' => json_encode(Geometry::fromPoint($observationPointGeometry)),
                'values_description' => json_encode($observationPoint->dateTimeValuesDescription()),
                'values' => json_encode($observationPoint->dateTimeValues())
            ), array(
                'boundary_id' => $event->boundaryId()->toString(),
                'observation_point_id' => $observationPoint->id()->toString()
            )
        );
    }

    public function onBoundaryWasCloned(BoundaryWasCloned $event): void
    {
        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);

        $rows = $this->connection->fetchAll(
            sprintf('SELECT * FROM %s WHERE boundary_id = :boundary_id', Table::BOUNDARY_OBSERVATION_POINT_VALUES),
            array('boundary_id' => $event->fromBoundary()->toString())
        );

        if ($rows === false) {
            return;
        }

        foreach ($rows as $row) {
            $this->connection->insert(Table::BOUNDARY_OBSERVATION_POINT_VALUES, array(
                'boundary_id' => $event->boundaryId()->toString(),
                'boundary_type' => $row['boundary_type'],
                'observation_point_id' => $row['observation_point_id'],
                'observation_point_name' => $row['observation_point_name'],
                'observation_point_geometry' => $row['observation_point_geometry'],
                'values_description' => $row['values_description'],
                'values' => $row['values']
            ));
        }
    }

    public function onBoundaryWasRemoved(BoundaryWasRemoved $event): void
    {
        $this->connection->delete(Table::BOUNDARY_OBSERVATION_POINT_VALUES, array(
            'boundary_id' => $event->boundaryId()->toString()
        ));
    }
}
