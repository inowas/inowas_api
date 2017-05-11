<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Infrastructure\Projection\BoundaryList;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Boundaries\ModflowBoundary;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Boundaries\BoundaryName;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\GeoTools\Service\GeoTools;
use Inowas\ModflowModel\Model\Event\BoundaryGeometryWasUpdated;
use Inowas\ModflowModel\Model\Event\BoundaryWasAdded;
use Inowas\ModflowModel\Model\Event\BoundaryWasRemoved;
use Inowas\ModflowModel\Model\Event\BoundaryWasUpdated;
use Inowas\Common\Id\ModflowId;
use Inowas\ModflowModel\Infrastructure\Projection\ModelList\ModelFinder;
use Inowas\ModflowModel\Infrastructure\Projection\Table;
use Inowas\ModflowModel\Model\Event\ModflowModelWasCloned;

class BoundaryListProjector extends AbstractDoctrineConnectionProjector
{

    /** @var  GeoTools */
    protected $geoTools;

    /** @var  ModelFinder */
    protected $modelDetailsFinder;

    /** @var  BoundaryFinder */
    protected $boundaryFinder;

    public function __construct(Connection $connection, GeoTools $geoTools, ModelFinder $modelDetailsFinder) {

        $this->geoTools = $geoTools;
        $this->modelDetailsFinder = $modelDetailsFinder;

        parent::__construct($connection);

        $this->schema = new Schema();
        $table = $this->schema->createTable(Table::BOUNDARIES);
        $table->addColumn('model_id', 'string', ['length' => 36]);
        $table->addColumn('boundary_id', 'string', ['length' => 36]);
        $table->addColumn('type', 'string', ['length' => 255]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('metadata', 'text', ['notnull' => false]);
        $table->addColumn('geometry', 'text', ['notnull' => false]);
        $table->addColumn('active_cells', 'text', ['notnull' => false]);
        $table->addColumn('boundary', 'text', ['notnull' => false]);
        $table->addIndex(array('model_id', 'boundary_id'));
    }

    public function onModflowModelWasCloned(ModflowModelWasCloned $event): void
    {
        foreach ($event->boundaries() as $boundary) {
            $this->cloneBoundaryWithNewModelIdAndNewUserId(
                BoundaryId::fromString($boundary),
                $event->baseModelId(),
                $event->modelId()
            );
        }
    }

    private function cloneBoundaryWithNewModelIdAndNewUserId(BoundaryId $boundaryId, ModflowId $baseModelId, ModflowId $newModelId): void
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT type, name, metadata, geometry, active_cells, boundary FROM %s WHERE model_id = :model_id AND boundary_id = :boundary_id', Table::BOUNDARIES),
            ['model_id' => $baseModelId->toString(), 'boundary_id' => $boundaryId->toString()]
        );

        if ($result === false){
            return;
        }

        $res = $this->connection->fetchAssoc(
            sprintf('SELECT count(*) FROM %s WHERE model_id = :model_id AND boundary_id = :boundary_id', Table::BOUNDARIES),
            ['model_id' => $newModelId->toString(), 'boundary_id' => $boundaryId->toString()]
        );

        if ($res['count'] == 0) {
            $this->connection->insert(Table::BOUNDARIES, array(
                'model_id' => $newModelId->toString(),
                'boundary_id' => $boundaryId->toString(),
                'name' => $result['name'],
                'geometry' => $result['geometry'],
                'type' => $result['type'],
                'metadata' => $result['metadata'],
                'active_cells' => $result['active_cells'],
                'boundary' => $result['boundary']
            ));
            return;
        }

        $this->connection->update(Table::BOUNDARIES, array(
            'name' => $result['name'],
            'geometry' => $result['geometry'],
            'type' => $result['type'],
            'metadata' => $result['metadata'],
            'active_cells' => $result['active_cells'],
            'boundary' => $result['boundary']
        ),array(
            'model_id' => $newModelId->toString(),
            'boundary_id' => $boundaryId->toString()
            )
        );
    }

    public function onBoundaryWasAdded(BoundaryWasAdded $event): void
    {
        $gridSize = $this->modelDetailsFinder->findGridSizeByModflowModelId($event->modflowId());
        $boundingBox = $this->modelDetailsFinder->findBoundingBoxByModflowModelId($event->modflowId());
        $activeCells = $this->geoTools->calculateActiveCellsFromBoundary($event->boundary(), $boundingBox, $gridSize);

        /** @var ModflowBoundary $boundary */
        $boundary = $event->boundary();
        $boundary = $boundary->setActiveCells($activeCells);

        $this->insertBoundary(
            $event->modflowId(),
            $event->boundary()->boundaryId(),
            $event->boundary()->name(),
            $event->boundary()->geometry()->toJson(),
            $event->boundary()->type(),
            json_encode($event->boundary()->metadata()),
            json_encode($activeCells->toArray()),
            base64_encode(serialize($boundary))
        );
    }

    public function onBoundaryWasRemoved(BoundaryWasRemoved $event): void
    {
        $this->connection->delete(Table::BOUNDARIES, array(
            'boundary_id' => $event->boundaryId()->toString(),
            'model_id' => $event->modflowId()->toString()
        ));
    }

    public function onBoundaryWasUpdated(BoundaryWasUpdated $event): void
    {
        $gridSize = $this->modelDetailsFinder->findGridSizeByModflowModelId($event->baseModelId());
        $boundingBox = $this->modelDetailsFinder->findBoundingBoxByModflowModelId($event->baseModelId());
        $activeCells = $this->geoTools->calculateActiveCellsFromBoundary($event->boundary(), $boundingBox, $gridSize);

        /** @var ModflowBoundary $boundary */
        $boundary = $event->boundary();
        $boundary = $boundary->setActiveCells($activeCells);

        $this->connection->update(Table::BOUNDARIES, array(
            'name' => $event->boundary()->name()->toString(),
            'geometry' => $event->boundary()->geometry()->toJson(),
            'type' => $event->boundary()->type(),
            'metadata' => json_encode($event->boundary()->metadata()),
            'active_cells' => json_encode($activeCells->toArray()),
            'boundary' => base64_encode(serialize($boundary))
        ), array(
            'boundary_id' => $event->boundary()->boundaryId()->toString(),
            'model_id' => $event->modflowId()->toString()
        ));
    }

    public function onBoundaryGeometryWasUpdated(BoundaryGeometryWasUpdated $event): void
    {
        $gridSize = $this->modelDetailsFinder->findGridSizeByModflowModelId($event->modflowModelId());
        $boundingBox = $this->modelDetailsFinder->findBoundingBoxByModflowModelId($event->modflowModelId());
        $boundary = $this->getBoundaryById($event->modflowModelId(), $event->boundaryId());
        $boundary = $boundary->updateGeometry($event->geometry());
        $activeCells = $this->geoTools->calculateActiveCellsFromBoundary($boundary, $boundingBox, $gridSize);

        /** @var ModflowBoundary $boundary */
        $boundary = $boundary->setActiveCells($activeCells);

        $this->connection->update(Table::BOUNDARIES, array(
            'name' =>$boundary->name()->toString(),
            'geometry' =>$boundary->geometry()->toJson(),
            'type' =>$boundary->type(),
            'metadata' => json_encode($boundary->metadata()),
            'active_cells' => json_encode($activeCells->toArray()),
            'boundary' => base64_encode(serialize($boundary))
        ), array(
            'boundary_id' =>$boundary->boundaryId()->toString(),
            'model_id' => $event->modflowModelId()->toString()
        ));
    }

    private function insertBoundary(
        ModflowId $modelId,
        BoundaryId $boundaryId,
        BoundaryName $boundaryName,
        string $boundaryGeometry,
        string $boundaryType,
        string $metadata,
        string $activeCells,
        string $boundary
    ): void
    {
        $this->connection->insert(Table::BOUNDARIES, array(
            'model_id' => $modelId->toString(),
            'boundary_id' => $boundaryId->toString(),
            'name' => $boundaryName->toString(),
            'geometry' => $boundaryGeometry,
            'type' => $boundaryType,
            'metadata' => $metadata,
            'active_cells' => $activeCells,
            'boundary' => $boundary
        ));
    }

    private function getBoundaryById(ModflowId $modelId, BoundaryId $boundaryId): ModflowBoundary
    {
        $row = $this->connection->fetchAssoc(
            sprintf('SELECT boundary FROM %s WHERE model_id = :model_id AND boundary_id = :boundary_id', Table::BOUNDARIES),
            [
                'model_id' => $modelId->toString(),
                'boundary_id' => $boundaryId->toString()
            ]
        );

        return unserialize(base64_decode($row['boundary']));
    }
}
