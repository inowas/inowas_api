<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Infrastructure\Projection\BoundaryList;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\ModflowModel\Model\Event\AreaActiveCellsWereUpdated;
use Inowas\ModflowModel\Model\Event\AreaGeometryWasUpdated;
use Inowas\ModflowModel\Model\Event\BoundaryActiveCellsWereUpdated;
use Inowas\ModflowModel\Model\Event\BoundaryAffectedLayersWereUpdated;
use Inowas\ModflowModel\Model\Event\BoundaryGeometryWasUpdated;
use Inowas\ModflowModel\Model\Event\BoundaryWasAdded;
use Inowas\ModflowModel\Model\Event\BoundaryWasRemoved;
use Inowas\ModflowModel\Model\Event\BoundingBoxWasChanged;
use Inowas\ModflowModel\Model\Event\GridSizeWasChanged;
use Inowas\ModflowModel\Model\Event\ModflowModelWasCloned;
use Inowas\ModflowModel\Infrastructure\Projection\Table;
use Inowas\ModflowModel\Model\Event\ModflowModelWasCreated;

class BoundaryActiveCellsProjector extends AbstractDoctrineConnectionProjector
{

    public function __construct(Connection $connection) {

        parent::__construct($connection);

        $schema = new Schema();
        $table = $schema->createTable(Table::BOUNDARY_ACTIVE_CELLS);
        $table->addColumn('model_id', 'string', ['length' => 36]);
        $table->addColumn('boundary_id', 'string', ['length' => 36]);
        $table->addColumn('active_cells', 'text', ['notnull' => false, 'default' => null]);
        $table->addIndex(array('model_id', 'boundary_id'));
        $this->addSchema($schema);
    }

    public function onAreaActiveCellsWereUpdated(AreaActiveCellsWereUpdated $event): void
    {
        $this->connection->update(Table::BOUNDARY_ACTIVE_CELLS, array(
            'active_cells' => json_encode($event->activeCells()->toArray())
        ), array (
            'model_id' => $event->modflowId()->toString(),
            'boundary_id' => $event->modflowId()->toString()
        ));
    }

    public function onAreaGeometryWasUpdated(AreaGeometryWasUpdated $event): void
    {
        $this->connection->update(Table::BOUNDARY_ACTIVE_CELLS, array(
            'active_cells' => null
        ), array(
            'model_id' => $event->modelId()->toString(),
            'boundary_id' => $event->modelId()->toString(),
        ));
    }

    public function onBoundaryActiveCellsWereUpdated(BoundaryActiveCellsWereUpdated $event): void
    {
        $this->connection->update(Table::BOUNDARY_ACTIVE_CELLS, array(
            'active_cells' => json_encode($event->activeCells()->toArray())
        ), array (
            'model_id' => $event->modelId()->toString(),
            'boundary_id' => $event->boundaryId()->toString()
        ));
    }

    public function onBoundaryAffectedLayersWereUpdated(BoundaryAffectedLayersWereUpdated $event): void
    {
        $this->connection->update(Table::BOUNDARY_ACTIVE_CELLS, array(
            'active_cells' => null
        ), array(
            'model_id' => $event->modflowModelId()->toString(),
            'boundary_id' => $event->boundaryId()->toString(),
        ));
    }

    public function onBoundaryGeometryWasUpdated(BoundaryGeometryWasUpdated $event): void
    {
        $this->connection->update(Table::BOUNDARY_ACTIVE_CELLS, array(
            'active_cells' => null
        ), array(
            'model_id' => $event->modflowModelId()->toString(),
            'boundary_id' => $event->boundaryId()->toString(),
        ));
    }

    public function onBoundaryWasAdded(BoundaryWasAdded $event): void
    {
        $this->connection->insert(Table::BOUNDARY_ACTIVE_CELLS, array(
            'model_id' => $event->modflowId()->toString(),
            'boundary_id' => $event->boundary()->boundaryId()->toString(),
            'active_cells' => null
        ));
    }

    public function onBoundaryWasRemoved(BoundaryWasRemoved $event): void
    {
        $this->connection->delete(Table::BOUNDARY_ACTIVE_CELLS, array(
            'model_id' => $event->modflowId()->toString(),
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
        $this->cloneArea($event->baseModelId(), $event->modelId());
        $this->cloneBoundaries($event->baseModelId(), $event->modelId());
    }

    public function onModflowModelWasCreated(ModflowModelWasCreated $event): void
    {
        $this->connection->insert(Table::BOUNDARY_ACTIVE_CELLS, array(
            'model_id' => $event->modelId()->toString(),
            'boundary_id' => $event->modelId()->toString(),
            'active_cells' => null
        ));
    }

    private function updateActiveCellsWithBoundingBoxOrGridsize(ModflowId $modelId): void
    {
        $rows = $this->connection->fetchAll(sprintf('SELECT * FROM %s WHERE model_id = :model_id', Table::BOUNDARY_ACTIVE_CELLS),
            array('model_id' => $modelId->toString())
        );

        foreach ($rows as $row){
            $boundaryId = BoundaryId::fromString($row['boundary_id']);

            $this->connection->update(Table::BOUNDARY_ACTIVE_CELLS, array(
                'active_cells' => null
            ), array(
                'model_id' => $modelId->toString(),
                'boundary_id' => $boundaryId->toString(),
            ));
        }

        $this->connection->update(Table::BOUNDARY_ACTIVE_CELLS, array(
            'active_cells' => null
        ), array(
            'model_id' => $modelId->toString(),
            'boundary_id' => $modelId->toString(),
        ));

    }

    private function cloneArea(ModflowId $baseModelId, ModflowId $modelId): void
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT * FROM %s WHERE model_id = :model_id AND boundary_id = :boundary_id', Table::BOUNDARY_ACTIVE_CELLS),
            ['model_id' => $baseModelId->toString(), 'boundary_id' => $baseModelId->toString()]
        );

        if ($result === false){
            return;
        }

        $this->connection->insert(Table::BOUNDARY_ACTIVE_CELLS, array(
            'model_id' => $modelId->toString(),
            'boundary_id' => $modelId->toString(),
            'active_cells' => $result['active_cells'],
        ));
    }

    private function cloneBoundaries(ModflowId $baseModelId, ModflowId $modelId): void
    {
        $rows = $this->connection->fetchAll(sprintf('SELECT * FROM %s WHERE model_id = :model_id AND NOT boundary_id = :boundary_id', Table::BOUNDARY_ACTIVE_CELLS),
            ['model_id' => $baseModelId->toString(), 'boundary_id' => $baseModelId->toString()]
        );

        if ($rows === false){
            return;
        }

        foreach ($rows as $row){
            $this->connection->insert(Table::BOUNDARY_ACTIVE_CELLS, array(
                'model_id' => $modelId->toString(),
                'boundary_id' => $row['boundary_id'],
                'active_cells' => $row['active_cells'],
            ));
        }
    }
}
