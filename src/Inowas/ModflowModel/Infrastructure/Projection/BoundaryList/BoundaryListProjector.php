<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Infrastructure\Projection\BoundaryList;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Boundaries\ObservationPoint;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\ModflowModel\Model\Event\BoundaryAffectedLayersWereUpdated;
use Inowas\ModflowModel\Model\Event\BoundaryGeometryWasUpdated;
use Inowas\ModflowModel\Model\Event\BoundaryMetadataWasUpdated;
use Inowas\ModflowModel\Model\Event\BoundaryNameWasUpdated;
use Inowas\ModflowModel\Model\Event\BoundaryWasAdded;
use Inowas\ModflowModel\Model\Event\BoundaryWasRemoved;
use Inowas\ModflowModel\Infrastructure\Projection\Table;
use Inowas\ModflowModel\Model\Event\ModflowModelWasCloned;

class BoundaryListProjector extends AbstractDoctrineConnectionProjector
{

    /** @var  BoundaryFinder */
    protected $boundaryFinder;

    public function __construct(Connection $connection) {

        parent::__construct($connection);

        $schema = new Schema();
        $table = $schema->createTable(Table::BOUNDARY_LIST);
        $table->addColumn('model_id', 'string', ['length' => 36]);
        $table->addColumn('boundary_id', 'string', ['length' => 36]);
        $table->addColumn('type', 'string', ['length' => 255]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('metadata', 'text', ['notnull' => false]);
        $table->addColumn('geometry', 'text', ['notnull' => false]);
        $table->addColumn('affected_layers', 'text', ['notnull' => false]);
        $table->addColumn('observation_point_ids', 'text', ['notnull' => false, 'default' => '[]']);
        $table->addIndex(array('model_id', 'boundary_id'));
        $this->addSchema($schema);
    }

    public function onBoundaryAffectedLayersWereUpdated(BoundaryAffectedLayersWereUpdated $event): void
    {

        $this->connection->update(Table::BOUNDARY_LIST, array(
            'affected_layers' => json_encode($event->affectedLayers()->toArray()),
        ), array(
            'boundary_id' => $event->boundaryId()->toString(),
            'model_id' => $event->modflowModelId()->toString()
        ));
    }

    public function onBoundaryGeometryWasUpdated(BoundaryGeometryWasUpdated $event): void
    {
        $this->connection->update(Table::BOUNDARY_LIST, array(
            'geometry' => json_encode($event->geometry()->toArray()),
        ), array(
            'boundary_id' => $event->boundaryId()->toString(),
            'model_id' => $event->modflowModelId()->toString()
        ));
    }

    public function onBoundaryMetadataWasUpdated(BoundaryMetadataWasUpdated $event): void
    {
        $this->connection->update(Table::BOUNDARY_LIST, array(
            'metadata' => json_encode($event->metadata()->toArray()),
        ), array(
            'boundary_id' => $event->boundaryId()->toString(),
            'model_id' => $event->modflowModelId()->toString()
        ));
    }

    public function onBoundaryNameWasUpdated(BoundaryNameWasUpdated $event): void
    {
        $this->connection->update(Table::BOUNDARY_LIST, array(
            'name' => $event->boundaryName()->toString(),
        ), array(
            'boundary_id' => $event->boundaryId()->toString(),
            'model_id' => $event->modflowModelId()->toString()
        ));
    }

    public function onBoundaryWasAdded(BoundaryWasAdded $event): void
    {

        /** @var ObservationPoint $observationPoint */
        $observationPointIds = array();
        foreach ($event->boundary()->observationPoints() as $observationPoint){
            $observationPointIds[] = $observationPoint->id()->toString();
        }

        $this->connection->insert(Table::BOUNDARY_LIST, array(
            'model_id' => $event->modflowId()->toString(),
            'boundary_id' => $event->boundary()->boundaryId()->toString(),
            'name' => $event->boundary()->name()->toString(),
            'geometry' => json_encode($event->boundary()->geometry()->toArray()),
            'type' => $event->boundary()->type()->toString(),
            'metadata' => json_encode($event->boundary()->metadata()),
            'observation_point_ids' => json_encode($observationPointIds),
            'affected_layers' => json_encode($event->boundary()->affectedLayers()->toArray()),
        ));
    }

    public function onBoundaryWasRemoved(BoundaryWasRemoved $event): void
    {
        $this->connection->delete(Table::BOUNDARY_LIST, array(
            'boundary_id' => $event->boundaryId()->toString(),
            'model_id' => $event->modflowId()->toString()
        ));
    }

    public function onModflowModelWasCloned(ModflowModelWasCloned $event): void
    {
        foreach ($event->boundaryIds() as $boundaryId) {
            $result = $this->connection->fetchAssoc(
                sprintf('SELECT * FROM %s WHERE model_id = :model_id AND boundary_id = :boundary_id', Table::BOUNDARY_LIST),
                ['model_id' => $event->baseModelId()->toString(), 'boundary_id' => $boundaryId]
            );

            $this->connection->insert(Table::BOUNDARY_LIST, array(
                'model_id' => $event->modelId()->toString(),
                'boundary_id' => $boundaryId,
                'type' => $result['type'],
                'name' => $result['name'],
                'metadata' => $result['metadata'],
                'geometry' => $result['geometry'],
                'affected_layers' => $result['affected_layers'],
                'observation_point_ids' => $result['observation_point_ids']
            ));
        }
    }
}
