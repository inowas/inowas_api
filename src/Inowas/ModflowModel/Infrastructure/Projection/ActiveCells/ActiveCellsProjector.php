<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Infrastructure\Projection\ActiveCells;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\ModflowModel\Model\Event\ActiveCellsWereUpdated;
use Inowas\ModflowModel\Model\Event\AreaGeometryWasUpdated;
use Inowas\ModflowModel\Model\Event\BoundaryWasAdded;
use Inowas\ModflowModel\Model\Event\BoundaryWasRemoved;
use Inowas\ModflowModel\Model\Event\BoundaryWasUpdated;
use Inowas\ModflowModel\Model\Event\BoundingBoxWasChanged;
use Inowas\ModflowModel\Model\Event\GridSizeWasChanged;
use Inowas\ModflowModel\Model\Event\ModflowModelWasCloned;
use Inowas\ModflowModel\Infrastructure\Projection\Table;
use Inowas\ModflowModel\Model\Event\ModflowModelWasCreated;

class ActiveCellsProjector extends AbstractDoctrineConnectionProjector
{

    public function __construct(Connection $connection) {

        parent::__construct($connection);

        $schema = new Schema();
        $table = $schema->createTable(Table::ACTIVE_CELLS);
        $table->addColumn('boundary_id', 'string', ['length' => 36, 'notnull' => false]);
        $table->addColumn('model_id', 'string', ['length' => 36]);
        $table->addColumn('active_cells', 'text', ['notnull' => false, 'default' => null]);
        $table->addIndex(array('model_id'));
        $this->addSchema($schema);
    }


    # ModflowModelAggregate Events
    public function onActiveCellsWereUpdated(ActiveCellsWereUpdated $event): void
    {
        $boundaryId = $event->boundaryId();
        if ($boundaryId instanceof BoundaryId) {
            $boundaryId = $boundaryId->toString();
        }

        $this->connection->update(Table::ACTIVE_CELLS, array(
            'active_cells' => json_encode($event->activeCells()->toArray())
        ), array (
            'model_id' => $event->modelId()->toString(),
            'boundary_id' => $boundaryId
        ));
    }

    public function onAreaGeometryWasUpdated(AreaGeometryWasUpdated $event): void
    {
        $this->connection->update(Table::ACTIVE_CELLS, array(
            'active_cells' => null
        ), array(
            'model_id' => $event->modelId()->toString(),
            'boundary_id' => null
        ));
    }

    public function onBoundaryWasAdded(BoundaryWasAdded $event): void
    {
        $this->connection->update(Table::ACTIVE_CELLS, array(
            'active_cells' => null
        ), array(
            'model_id' => $event->modelId()->toString(),
            'boundary_id' => $event->boundaryId()->toString()
        ));
    }

    public function onBoundaryWasRemoved(BoundaryWasRemoved $event): void
    {
        $this->connection->delete(Table::ACTIVE_CELLS, array(
            'model_id' => $event->modelId()->toString(),
            'boundary_id' => $event->boundaryId()
        ));
    }

    public function onBoundaryWasUpdated(BoundaryWasUpdated $event): void
    {
        $this->connection->update(Table::ACTIVE_CELLS, array(
            'active_cells' => null
        ), array(
            'model_id' => $event->modelId()->toString(),
            'boundary_id' => $event->boundaryId()->toString()
        ));
    }

    public function onBoundingBoxWasChanged(BoundingBoxWasChanged $event): void
    {
        $this->updateActiveCellsWithBoundingBoxOrGridsize($event->modflowId());
    }

    public function onGridSizeWasChanged(GridSizeWasChanged $event): void
    {
        $this->updateActiveCellsWithBoundingBoxOrGridsize($event->modflowId());
    }

    public function onModflowModelWasCloned(ModflowModelWasCloned $event): void
    {
        $rows = $this->connection->fetchAll(
            sprintf('SELECT boundary_id, active_cells FROM %s WHERE model_id = :model_id', Table::ACTIVE_CELLS),
            ['model_id' => $event->baseModelId()->toString()]
        );

        if ($rows === false){
            return;
        }

        foreach ($rows as $row) {
            $this->connection->insert(Table::ACTIVE_CELLS, array(
                'boundary_id' => $row['boundary_id'],
                'model_id' => $event->modelId()->toString(),
                'active_cells' => $row['active_cells'],
            ));
        }

    }

    public function onModflowModelWasCreated(ModflowModelWasCreated $event): void
    {
        $this->connection->insert(Table::ACTIVE_CELLS, array(
            'model_id' => $event->modelId()->toString(),
            'boundary_id' => $event->modelId()->toString(),
            'active_cells' => null
        ));
    }

    # Helpers
    private function updateActiveCellsWithBoundingBoxOrGridsize(ModflowId $modelId): void
    {
        $this->connection->update(Table::ACTIVE_CELLS, array(
            'active_cells' => null
        ), array(
            'model_id' => $modelId->toString()
        ));
    }
}
