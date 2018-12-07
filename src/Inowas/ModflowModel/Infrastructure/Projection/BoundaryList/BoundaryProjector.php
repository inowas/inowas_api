<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Infrastructure\Projection\BoundaryList;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\ModflowModel\Infrastructure\Projection\Table;
use Inowas\ModflowModel\Model\Event\ActiveCellsWereUpdated;
use Inowas\ModflowModel\Model\Event\BoundaryWasAdded;
use Inowas\ModflowModel\Model\Event\BoundaryWasRemoved;
use Inowas\ModflowModel\Model\Event\BoundaryWasUpdated;
use Inowas\ModflowModel\Model\Event\ModflowModelWasCloned;
use Inowas\ModflowModel\Model\Event\ModflowModelWasDeleted;

class BoundaryProjector extends AbstractDoctrineConnectionProjector
{

    public function __construct(Connection $connection) {

        parent::__construct($connection);

        $schema = new Schema();
        $table = $schema->createTable(Table::BOUNDARIES);
        $table->addColumn('model_id', 'string', ['length' => 36, 'notnull' => false]);
        $table->addColumn('boundary_id', 'string', ['length' => 36, 'notnull' => false]);
        $table->addColumn('type', 'string', ['length' => 3]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('geometry', 'text', ['notnull' => false]);
        $table->addColumn('active_cells', 'text', ['notnull' => false]);
        $table->addColumn('metadata', 'text', ['notnull' => false]);
        $table->addColumn('affected_layers', 'text', ['notnull' => false]);
        $table->addColumn('boundary', 'text', ['notnull' => false]);
        $table->setPrimaryKey(['model_id', 'boundary_id']);
        $table->addIndex(['boundary_id']);
        $this->addSchema($schema);
    }

    public function onBoundaryWasAdded(BoundaryWasAdded $event): void
    {
        $boundary = $event->boundary();

        $result = $this->connection->fetchAssoc(
            sprintf('SELECT count(*) FROM %s WHERE model_id = :model_id AND boundary_id = :boundary_id', Table::BOUNDARIES),
            ['model_id' => $event->modelId()->toString(), 'boundary_id' => $boundary->boundaryId()->toString()]
        );

        if ($result && $result['count'] > 0) {
            return;
        }

        $this->connection->insert(Table::BOUNDARIES, array(
            'boundary_id' => $boundary->boundaryId()->toString(),
            'model_id' => $event->modelId()->toString(),
            'type' => $boundary->type()->toString(),
            'name' => $boundary->name()->toString(),
            'geometry' => json_encode($boundary->geometry()->toArray()),
            'active_cells' => json_encode($boundary->affectedCells()->toArray()),
            'metadata' => json_encode($boundary->metadata()->toArray()),
            'affected_layers' => json_encode($boundary->affectedLayers()->toArray()),
            'boundary' => json_encode($boundary->toArray())
        ));
    }

    public function onBoundaryWasUpdated(BoundaryWasUpdated $event): void
    {
        $boundary = $event->boundary();

        $this->connection->update(Table::BOUNDARIES, array(
            'type' => $boundary->type()->toString(),
            'name' => $boundary->name()->toString(),
            'geometry' => json_encode($boundary->geometry()->toArray()),
            'active_cells' => json_encode($boundary->affectedCells()->toArray()),
            'metadata' => json_encode($boundary->metadata()->toArray()),
            'affected_layers' => json_encode($boundary->affectedLayers()->toArray()),
            'boundary' => json_encode($boundary->toArray())
        ), array(
            'boundary_id' => $event->boundaryId()->toString(),
            'model_id' => $event->modelId()->toString(),
        ));
    }

    /**
     * @param BoundaryWasRemoved $event
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function onBoundaryWasRemoved(BoundaryWasRemoved $event): void
    {
        $this->connection->delete(Table::BOUNDARIES, array(
            'boundary_id' => $event->boundaryId()->toString(),
            'model_id' => $event->modelId()->toString()
        ));
    }

    public function onModflowmodelWasCloned(ModflowModelWasCloned $event): void
    {
        $rows = $this->connection->fetchAll(
            sprintf('SELECT * FROM %s WHERE model_id = :model_id', Table::BOUNDARIES),
            ['model_id' => $event->baseModelId()->toString()]
        );

        if ($rows === false) {
            return;
        }

        foreach ($rows as $row) {
            $this->connection->insert(Table::BOUNDARIES, array(
                'boundary_id' => $row['boundary_id'],
                'model_id' => $event->modelId()->toString(),
                'type' => $row['type'],
                'name' => $row['name'],
                'geometry' => $row['geometry'],
                'active_cells' => $row['active_cells'],
                'metadata' => $row['metadata'],
                'affected_layers' => $row['affected_layers'],
                'boundary' => $row['boundary']
            ));
        }
    }

    /**
     * @param ModflowModelWasDeleted $event
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function onModflowModelWasDeleted(ModflowModelWasDeleted $event): void
    {
        $this->connection->delete(
            Table::BOUNDARIES, array('model_id' => $event->modelId()->toString())
        );
    }

    public function onActiveCellsWereUpdated(ActiveCellsWereUpdated $activeCellsWereUpdated): void
    {
        // TODO
        // Remove from config
    }
}
