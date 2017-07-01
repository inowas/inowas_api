<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Infrastructure\Projection\BoundaryList;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\ModflowModel\Model\Event\Boundary\BoundaryAffectedLayersWereUpdated;
use Inowas\ModflowModel\Model\Event\Boundary\BoundaryGeometryWasUpdated;
use Inowas\ModflowModel\Model\Event\Boundary\BoundaryMetadataWasUpdated;
use Inowas\ModflowModel\Model\Event\Boundary\BoundaryNameWasUpdated;
use Inowas\ModflowModel\Model\Event\Boundary\BoundaryWasCloned;
use Inowas\ModflowModel\Model\Event\Boundary\BoundaryWasAdded;
use Inowas\ModflowModel\Model\Event\Boundary\BoundaryWasRemoved;
use Inowas\ModflowModel\Infrastructure\Projection\Table;

class BoundaryListProjector extends AbstractDoctrineConnectionProjector
{

    public function __construct(Connection $connection) {

        parent::__construct($connection);

        $schema = new Schema();
        $table = $schema->createTable(Table::BOUNDARY_LIST);
        $table->addColumn('boundary_id', 'string', ['length' => 36]);
        $table->addColumn('model_id', 'string', ['length' => 36, 'notnull' => false]);
        $table->addColumn('type', 'string', ['length' => 255]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('geometry', 'text', ['notnull' => false]);
        $table->addColumn('metadata', 'text', ['notnull' => false]);
        $table->addColumn('affected_layers', 'text', ['notnull' => false]);
        $table->addIndex(array('boundary_id'));
        $this->addSchema($schema);
    }

    public function onBoundaryAffectedLayersWereUpdated(BoundaryAffectedLayersWereUpdated $event): void
    {

        $this->connection->update(Table::BOUNDARY_LIST, array(
            'affected_layers' => json_encode($event->affectedLayers()->toArray()),
        ), array(
            'boundary_id' => $event->boundaryId()->toString()
        ));
    }

    public function onBoundaryGeometryWasUpdated(BoundaryGeometryWasUpdated $event): void
    {
        $this->connection->update(Table::BOUNDARY_LIST, array(
            'geometry' => json_encode($event->geometry()->toArray()),
        ), array(
            'boundary_id' => $event->boundaryId()->toString()
        ));
    }

    public function onBoundaryMetadataWasUpdated(BoundaryMetadataWasUpdated $event): void
    {
        $this->connection->update(Table::BOUNDARY_LIST, array(
            'metadata' => json_encode($event->metadata()->toArray()),
        ), array(
            'boundary_id' => $event->boundaryId()->toString()
        ));
    }

    public function onBoundaryNameWasUpdated(BoundaryNameWasUpdated $event): void
    {
        $this->connection->update(Table::BOUNDARY_LIST, array(
            'name' => $event->boundaryName()->toString(),
        ), array(
            'boundary_id' => $event->boundaryId()->toString()
        ));
    }

    public function onBoundaryWasAdded(BoundaryWasAdded $event): void
    {
        $this->connection->insert(Table::BOUNDARY_LIST, array(
            'boundary_id' => $event->boundaryId()->toString(),
            'model_id' => $event->modelId()->toString(),
            'type' => $event->type()->toString(),
            'name' => $event->name()->toString(),
            'geometry' => json_encode($event->geometry()->toArray()),
            'metadata' => json_encode($event->metadata()),
            'affected_layers' => json_encode($event->affectedLayers()->toArray())
        ));
    }

    public function onBoundaryWasCloned(BoundaryWasCloned $event): void
    {
        $row = $this->connection->fetchAssoc(
            sprintf('SELECT * FROM %s WHERE boundary_id = :boundary_id', Table::BOUNDARY_LIST),
            ['boundary_id' => $event->fromBoundary()->toString()]
        );

        if ($row === false) {
            return;
        }

        $this->connection->insert(Table::BOUNDARY_LIST, array(
            'boundary_id' => $event->boundaryId()->toString(),
            'model_id' => $event->modelId()->toString(),
            'type' => $row['type'],
            'name' => $row['name'],
            'geometry' => $row['geometry'],
            'metadata' => $row['metadata'],
            'affected_layers' => $row['affected_layers']
        ));
    }

    public function onBoundaryWasRemoved(BoundaryWasRemoved $event): void
    {
        $this->connection->delete(Table::BOUNDARY_LIST, array(
            'boundary_id' => $event->boundaryId()->toString()
        ));
    }
}
