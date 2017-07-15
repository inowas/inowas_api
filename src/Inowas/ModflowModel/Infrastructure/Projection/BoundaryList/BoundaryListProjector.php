<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Infrastructure\Projection\BoundaryList;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Boundaries\BoundaryFactory;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\ModflowModel\Infrastructure\Projection\Table;
use Inowas\ModflowModel\Model\Event\BoundaryWasAdded;
use Inowas\ModflowModel\Model\Event\BoundaryWasRemoved;
use Inowas\ModflowModel\Model\Event\BoundaryWasUpdated;
use Inowas\ModflowModel\Model\Event\ModflowModelWasCloned;
use Inowas\ModflowModel\Model\Event\ModflowModelWasDeleted;

class BoundaryListProjector extends AbstractDoctrineConnectionProjector
{

    public function __construct(Connection $connection) {

        parent::__construct($connection);

        $schema = new Schema();
        $table = $schema->createTable(Table::BOUNDARIES_LIST);
        $table->addColumn('model_id', 'string', ['length' => 36, 'notnull' => false]);
        $table->addColumn('boundary_id', 'string', ['length' => 36, 'notnull' => false]);
        $table->addColumn('type', 'string', ['length' => 3]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('geometry', 'text', ['notnull' => false]);
        $table->addColumn('metadata', 'text', ['notnull' => false]);
        $table->addColumn('affected_layers', 'text', ['notnull' => false]);
        $table->addColumn('boundary', 'text', ['notnull' => false]);
        $table->addIndex(array('boundary_id', 'model_id'));
        $this->addSchema($schema);
    }

    public function onBoundaryWasAdded(BoundaryWasAdded $event): void
    {
        $boundary = $event->boundary();

        $this->connection->insert(Table::BOUNDARIES_LIST, array(
            'boundary_id' => $event->boundaryId()->toString(),
            'model_id' => $event->modelId()->toString(),
            'type' => $boundary->type()->toString(),
            'name' => $boundary->name()->toString(),
            'geometry' => json_encode($boundary->geometry()->toArray()),
            'metadata' => json_encode($boundary->metadata()),
            'affected_layers' => json_encode($boundary->affectedLayers()->toArray()),
            'boundary' => BoundaryFactory::serialize($boundary)
        ));
    }

    public function onBoundaryWasUpdated(BoundaryWasUpdated $event): void
    {
        $boundary = $event->boundary();

        $this->connection->update(Table::BOUNDARIES_LIST, array(
            'type' => $boundary->type()->toString(),
            'name' => $boundary->name()->toString(),
            'geometry' => json_encode($boundary->geometry()->toArray()),
            'metadata' => json_encode($boundary->metadata()),
            'affected_layers' => json_encode($boundary->affectedLayers()->toArray()),
            'boundary' => BoundaryFactory::serialize($boundary)
        ), array(
            'boundary_id' => $event->boundaryId()->toString(),
            'model_id' => $event->modelId()->toString(),
        ));
    }

    public function onBoundaryWasRemoved(BoundaryWasRemoved $event): void
    {
        $this->connection->delete(Table::BOUNDARIES_LIST, array(
            'boundary_id' => $event->boundaryId()->toString(),
            'model_id' => $event->modelId()->toString()
        ));
    }

    public function onModflowmodelWasCloned(ModflowModelWasCloned $event): void
    {
        $rows = $this->connection->fetchAll(
            sprintf('SELECT * FROM %s WHERE model_id = :model_id', Table::BOUNDARIES_LIST),
            ['model_id' => $event->baseModelId()->toString()]
        );

        if ($rows === false) {
            return;
        }

        foreach ($rows as $row) {
            $this->connection->insert(Table::BOUNDARIES_LIST, array(
                'boundary_id' => $row['boundary_id'],
                'model_id' => $event->modelId()->toString(),
                'type' => $row['type'],
                'name' => $row['name'],
                'geometry' => $row['geometry'],
                'metadata' => $row['metadata'],
                'affected_layers' => $row['affected_layers'],
                'boundary' => $row['boundary']
            ));
        }
    }

    public function onModflowModelWasDeleted(ModflowModelWasDeleted $event): void
    {
        $this->connection->delete(
            Table::BOUNDARIES_LIST, array('model_id' => $event->modelId()->toString())
        );
    }
}
