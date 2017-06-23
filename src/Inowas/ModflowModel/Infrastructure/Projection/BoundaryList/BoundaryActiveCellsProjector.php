<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Infrastructure\Projection\BoundaryList;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Grid\AffectedLayers;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Grid\LayerNumber;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\GeoTools\Service\GeoTools;
use Inowas\ModflowModel\Infrastructure\Projection\ModelList\ModelFinder;
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
    /** @var  BoundaryFinder */
    private $boundaryFinder;

    /** @var  GeoTools */
    private $geoTools;

    /** @var  ModelFinder */
    private $modelFinder;

    public function __construct(Connection $connection, ModelFinder $modelFinder, BoundaryFinder $boundaryFinder, GeoTools $geoTools) {

        parent::__construct($connection);

        $this->boundaryFinder = $boundaryFinder;
        $this->geoTools = $geoTools;
        $this->modelFinder = $modelFinder;

        $schema = new Schema();
        $table = $schema->createTable(Table::BOUNDARY_ACTIVE_CELLS);
        $table->addColumn('model_id', 'string', ['length' => 36]);
        $table->addColumn('boundary_id', 'string', ['length' => 36]);
        $table->addColumn('active_cells', 'text', ['notnull' => false]);
        $table->addIndex(array('model_id', 'boundary_id'));
        $this->addSchema($schema);
    }

    public function onAreaActiveCellsWereUpdated(AreaActiveCellsWereUpdated $event): void
    {
        $this->connection->update(Table::BOUNDARY_ACTIVE_CELLS, array(
            'active_cells' => $event->activeCells()
        ), array (
            'model_id' => $event->modflowId()->toString(),
            'boundary_id' => $event->modflowId()->toString()
        ));
    }

    public function onAreaGeometryWasUpdated(AreaGeometryWasUpdated $event): void
    {
        $affectedLayers = AffectedLayers::createWithLayerNumber(LayerNumber::fromInteger(0));
        $boundingBox = $this->modelFinder->getBoundingBoxByModflowModelId($event->modelId());
        $geometry = Geometry::fromPolygon($event->geometry());
        $gridSize = $this->modelFinder->getGridSizeByModflowModelId($event->modelId());
        $activeCells = $this->geoTools->calculateActiveCellsFromGeometryAndAffectedLayers($geometry, $affectedLayers, $boundingBox, $gridSize);

        $this->connection->update(Table::BOUNDARY_ACTIVE_CELLS, array(
            'active_cells' => json_encode($activeCells->toArray())
        ), array(
            'model_id' => $event->modelId()->toString(),
            'boundary_id' => $event->modelId()->toString(),
        ));
    }

    public function onBoundaryActiveCellsWereUpdated(BoundaryActiveCellsWereUpdated $event): void
    {
        $this->connection->update(Table::BOUNDARY_ACTIVE_CELLS, array(
            'active_cells' => json_encode($event->activeCells())
        ), array (
            'model_id' => $event->modelId()->toString(),
            'boundary_id' => $event->boundaryId()->toString()
        ));
    }

    public function onBoundaryAffectedLayersWereUpdated(BoundaryAffectedLayersWereUpdated $event): void
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT active_cells from %s WHERE model_id = :model_id AND boundary_id = :boundary_id', Table::BOUNDARY_ACTIVE_CELLS),
            ['model_id' => $event->modflowModelId()->toString(), 'boundary_id' => $event->boundaryId()->toString()]);

        if ($result === false){
            return;
        }

        $activeCells = ActiveCells::fromArray(json_decode($result['active_cells']));
        $updatedActiveCells = ActiveCells::fromArrayGridSizeAndLayer($activeCells->layerData(), $activeCells->gridSize(), $event->affectedLayers());

        $this->connection->update(Table::BOUNDARY_ACTIVE_CELLS, array(
            'active_cells' => json_encode($updatedActiveCells->toArray())
        ), array(
            'model_id' => $event->modflowModelId()->toString(),
            'boundary_id' => $event->boundaryId()->toString(),
        ));
    }

    public function onBoundaryGeometryWasUpdated(BoundaryGeometryWasUpdated $event): void
    {
        $affectedLayers = $this->boundaryFinder->getAffectedLayersByModelAndBoundary($event->modflowModelId(), $event->boundaryId());
        $boundingBox = $this->modelFinder->getBoundingBoxByModflowModelId($event->modflowModelId());
        $geometry = $event->geometry();
        $gridSize = $this->modelFinder->getGridSizeByModflowModelId($event->modflowModelId());
        $activeCells = $this->geoTools->calculateActiveCellsFromGeometryAndAffectedLayers($geometry, $affectedLayers, $boundingBox, $gridSize);

        $this->connection->update(Table::BOUNDARY_ACTIVE_CELLS, array(
            'active_cells' => json_encode($activeCells->toArray())
        ), array(
            'model_id' => $event->modflowModelId()->toString(),
            'boundary_id' => $event->boundaryId()->toString(),
        ));
    }

    public function onBoundaryWasAdded(BoundaryWasAdded $event): void
    {
        $boundary = $event->boundary();
        $boundingBox = $this->modelFinder->getBoundingBoxByModflowModelId($event->modflowId());
        $gridSize = $this->modelFinder->getGridSizeByModflowModelId($event->modflowId());
        $activeCells = $this->geoTools->calculateActiveCellsFromBoundary($boundary, $boundingBox, $gridSize);

        $this->connection->insert(Table::BOUNDARY_ACTIVE_CELLS, array(
            'model_id' => $event->modflowId()->toString(),
            'boundary_id' => $event->boundary()->boundaryId()->toString(),
            'active_cells' => json_encode($activeCells->toArray())
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
        $modelId = $event->modflowId();
        $boundingBox = $event->boundingBox();
        $gridSize = $this->modelFinder->getGridSizeByModflowModelId($event->modflowId());
        $this->updateActiveCellsWithBoundingBoxOrGridsize($modelId, $boundingBox, $gridSize);
    }

    public function onGridSizeWasChanged(GridSizeWasChanged $event): void
    {
        $modelId = $event->modflowId();
        $gridSize = $event->gridSize();
        $boundingBox = $this->modelFinder->getBoundingBoxByModflowModelId($event->modflowId());
        $this->updateActiveCellsWithBoundingBoxOrGridsize($modelId, $boundingBox, $gridSize);
    }

    public function onModflowModelWasCloned(ModflowModelWasCloned $event): void
    {
        $this->cloneArea($event->baseModelId(), $event->modelId());
        $this->cloneBoundaries($event->baseModelId(), $event->modelId());
    }

    public function onModflowModelWasCreated(ModflowModelWasCreated $event): void
    {
        $area = $this->modelFinder->getAreaByModflowModelId($event->modelId());
        $boundingBox = $this->modelFinder->getBoundingBoxByModflowModelId($event->modelId());
        $gridSize = $this->modelFinder->getGridSizeByModflowModelId($event->modelId());
        $activeCells = $this->geoTools->calculateActiveCellsFromArea($area, $boundingBox, $gridSize);

        $this->connection->insert(Table::BOUNDARY_ACTIVE_CELLS, array(
            'model_id' => $event->modelId()->toString(),
            'boundary_id' => $event->modelId()->toString(),
            'active_cells' => json_encode($activeCells->toArray())
        ));
    }

    private function updateActiveCellsWithBoundingBoxOrGridsize(ModflowId $modelId, BoundingBox $boundingBox, GridSize $gridSize): void
    {

        $rows = $this->connection->fetchAll(sprintf('SELECT * FROM %s WHERE model_id = :model_id', Table::BOUNDARY_ACTIVE_CELLS),
            array('model_id' => $modelId->toString())
        );

        foreach ($rows as $row){
            $boundaryId = BoundaryId::fromString($row['boundary_id']);
            $geometry = $this->boundaryFinder->getBoundaryGeometry($modelId, $boundaryId);

            if (null === $geometry){
                continue;
            }

            $affectedLayers = $this->boundaryFinder->getAffectedLayersByModelAndBoundary($modelId, $boundaryId);
            $newActiveCells = $this->geoTools->calculateActiveCellsFromGeometryAndAffectedLayers($geometry, $affectedLayers, $boundingBox, $gridSize);

            $this->connection->update(Table::BOUNDARY_ACTIVE_CELLS, array(
                'active_cells' => json_encode($newActiveCells->toArray())
            ), array(
                'model_id' => $modelId->toString(),
                'boundary_id' => $boundaryId->toString(),
            ));
        }
        $areaGeometry = Geometry::fromPolygon($this->modelFinder->getAreaPolygonByModflowModelId($modelId));
        $areaAffectedLayers = AffectedLayers::createWithLayerNumber(LayerNumber::fromInteger(0));
        $areaNewActiveCells = $this->geoTools->calculateActiveCellsFromGeometryAndAffectedLayers($areaGeometry, $areaAffectedLayers, $boundingBox, $gridSize);
        $this->connection->update(Table::BOUNDARY_ACTIVE_CELLS, array(
            'active_cells' => json_encode($areaNewActiveCells->toArray())
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
