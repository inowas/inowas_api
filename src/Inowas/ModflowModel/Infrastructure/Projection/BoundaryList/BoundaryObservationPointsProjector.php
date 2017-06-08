<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Infrastructure\Projection\BoundaryList;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Boundaries\ObservationPoint;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\ModflowModel\Model\Event\BoundaryWasAdded;
use Inowas\ModflowModel\Model\Event\BoundaryWasRemoved;
use Inowas\ModflowModel\Model\Event\ModflowModelWasCloned;
use Inowas\ModflowModel\Infrastructure\Projection\Table;

class BoundaryObservationPointsProjector extends AbstractDoctrineConnectionProjector
{

    public function __construct(Connection $connection) {

        parent::__construct($connection);

        $this->schema = new Schema();
        $table = $this->schema->createTable(Table::BOUNDARY_OBSERVATION_POINT_VALUES);
        $table->addColumn('model_id', 'string', ['length' => 36]);
        $table->addColumn('boundary_id', 'string', ['length' => 36]);
        $table->addColumn('boundary_type', 'string', ['length' => 255]);
        $table->addColumn('observation_point_id', 'string', ['length' => 36]);
        $table->addColumn('observation_point_name', 'string', ['length' => 255]);
        $table->addColumn('observation_point_geometry', 'text', ['notnull' => false]);
        $table->addColumn('values_description', 'text', ['notnull' => false]);
        $table->addColumn('values', 'text', ['notnull' => false]);
        $table->addIndex(array('model_id'));
    }

    public function onBoundaryWasAdded(BoundaryWasAdded $event): void
    {
        /** @var ObservationPoint $observationPoint */
        foreach ($event->boundary()->observationPoints() as $observationPoint) {
            $this->connection->insert(Table::BOUNDARY_OBSERVATION_POINT_VALUES, array(
                'model_id' => $event->modflowId()->toString(),
                'boundary_id' => $event->boundary()->boundaryId()->toString(),
                'boundary_type' => $event->boundary()->type(),
                'observation_point_id' => $observationPoint->id()->toString(),
                'observation_point_name' => $observationPoint->name()->toString(),
                'observation_point_geometry' => json_encode($observationPoint->geometry()->toArray()),
                'values_description' => json_encode($observationPoint->dateTimeValuesDescription()),
                'values' => json_encode($observationPoint->dateTimeValues())
            ));
        }
    }

    public function onBoundaryWasRemoved(BoundaryWasRemoved $event): void
    {
        $this->connection->delete(Table::BOUNDARY_OBSERVATION_POINT_VALUES, array(
            'model_id' => $event->modflowId()->toString(),
            'boundary_id' => $event->boundaryId()->toString()
        ));
    }

    public function onModflowModelWasCloned(ModflowModelWasCloned $event): void
    {

        $sql = sprintf("SELECT * FROM %s WHERE model_id = ?", Table::BOUNDARY_OBSERVATION_POINT_VALUES);
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(1, $event->baseModelId()->toString());
        $stmt->execute();
        $boundaries = $stmt->fetchAll();

        foreach ($boundaries as $boundary) {
            $this->connection->insert(Table::BOUNDARY_OBSERVATION_POINT_VALUES, array(
                'model_id' => $event->modelId()->toString(),
                'boundary_id' => $boundary['boundary_id'],
                'boundary_type' => $boundary['boundary_type'],
                'observation_point_id' => $boundary['observation_point_id'],
                'observation_point_name' => $boundary['observation_point_name'],
                'observation_point_geometry' => $boundary['observation_point_geometry'],
                'values_description' => $boundary['values_description'],
                'values' => $boundary['values']
            ));
        }
    }
}
