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
use Inowas\ModflowBoundary\Model\Event\BoundaryActiveCellsWereUpdated;
use Inowas\ModflowBoundary\Model\Event\BoundaryAffectedLayersWereUpdated;
use Inowas\ModflowBoundary\Model\Event\BoundaryGeometryWasUpdated;
use Inowas\ModflowBoundary\Model\Event\BoundaryWasAdded;
use Inowas\ModflowBoundary\Model\Event\BoundaryWasCloned;
use Inowas\ModflowBoundary\Model\Event\BoundaryWasRemoved;
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
        $table->addColumn('boundary_id', 'string', ['length' => 36]);
        $table->addColumn('model_id', 'string', ['length' => 36]);
        $table->addColumn('active_cells', 'text', ['notnull' => false, 'default' => null]);
        $table->addIndex(array('boundary_id', 'model_id'));
        $this->addSchema($schema);
    }

    # BoundaryAggregate Events
    public function onBoundaryActiveCellsWereUpdated(BoundaryActiveCellsWereUpdated $event): void
    {
        $this->connection->update(Table::ACTIVE_CELLS, array(
            'active_cells' => json_encode($event->activeCells()->toArray())
        ), array (
            'boundary_id' => $event->boundaryId()->toString()
        ));
    }

    public function onBoundaryAffectedLayersWereUpdated(BoundaryAffectedLayersWereUpdated $event): void
    {
        $this->connection->update(Table::ACTIVE_CELLS, array(
            'active_cells' => null
        ), array(
            'boundary_id' => $event->boundaryId()->toString(),
        ));
    }

    public function onBoundaryGeometryWasUpdated(BoundaryGeometryWasUpdated $event): void
    {
        $this->connection->update(Table::ACTIVE_CELLS, array(
            'active_cells' => null
        ), array(
            'boundary_id' => $event->boundaryId()->toString(),
        ));
    }

    public function onBoundaryWasAdded(BoundaryWasAdded $event): void
    {
        $this->connection->insert(Table::ACTIVE_CELLS, array(
            'boundary_id' => $event->boundaryId()->toString(),
            'model_id' => $event->modelId()->toString(),
            'active_cells' => null
        ));
    }

    public function onBoundaryWasCloned(BoundaryWasCloned $event): void
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT active_cells FROM %s WHERE boundary_id = :boundary_id', Table::ACTIVE_CELLS),
            ['boundary_id' => $event->boundaryId()->toString()]
        );

        if ($result === false){
            return;
        }

        $this->connection->insert(Table::ACTIVE_CELLS, array(
            'boundary_id' => $event->boundaryId()->toString(),
            'model_id' => $event->modelId()->toString(),
            'active_cells' => $result['active_cells']
        ));
    }

    public function onBoundaryWasRemoved(BoundaryWasRemoved $event): void
    {
        $this->connection->delete(Table::ACTIVE_CELLS, array(
            'boundary_id' => $event->boundaryId()->toString()
        ));
    }

    # ModflowModelAggregate Events
    public function onAreaActiveCellsWereUpdated(ActiveCellsWereUpdated $event): void
    {
        $this->connection->update(Table::ACTIVE_CELLS, array(
            'active_cells' => json_encode($event->activeCells()->toArray())
        ), array (
            'boundary_id' => $event->modflowId()->toString()
        ));
    }

    public function onAreaGeometryWasUpdated(AreaGeometryWasUpdated $event): void
    {
        $this->connection->update(Table::ACTIVE_CELLS, array(
            'active_cells' => null
        ), array(
            'boundary_id' => $event->modelId()->toString(),
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
        $this->cloneArea($event->baseModelId(), $event->modelId());
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
        $rows = $this->connection->fetchAll(sprintf('SELECT * FROM %s WHERE model_id = :model_id', Table::ACTIVE_CELLS),
            array('model_id' => $modelId->toString())
        );

        foreach ($rows as $row){
            $boundaryId = BoundaryId::fromString($row['boundary_id']);

            $this->connection->update(Table::ACTIVE_CELLS, array(
                'active_cells' => null
            ), array(
                'model_id' => $modelId->toString(),
                'boundary_id' => $boundaryId->toString(),
            ));
        }

        $this->connection->update(Table::ACTIVE_CELLS, array(
            'active_cells' => null
        ), array(
            'model_id' => $modelId->toString(),
            'boundary_id' => $modelId->toString(),
        ));

    }

    private function cloneArea(ModflowId $baseModelId, ModflowId $modelId): void
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT active_cells FROM %s WHERE boundary_id = :boundary_id', Table::ACTIVE_CELLS),
            ['boundary_id' => $baseModelId->toString()]
        );

        if ($result === false){
            return;
        }

        $this->connection->insert(Table::ACTIVE_CELLS, array(
            'boundary_id' => $modelId->toString(),
            'model_id' => $modelId->toString(),
            'active_cells' => $result['active_cells'],
        ));
    }
}
