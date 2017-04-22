<?php

declare(strict_types=1);

namespace Inowas\Modflow\Projection\BoundaryList;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Boundaries\ModflowBoundary;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Boundaries\BoundaryName;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\GeoTools\Model\GeoTools;
use Inowas\Modflow\Model\Event\BoundaryWasAdded;
use Inowas\Modflow\Model\Event\BoundaryWasAddedToScenario;
use Inowas\Modflow\Model\Event\BoundaryWasRemoved;
use Inowas\Modflow\Model\Event\BoundaryWasRemovedFromScenario;
use Inowas\Modflow\Model\Event\BoundaryWasUpdated;
use Inowas\Modflow\Model\Event\ModflowScenarioWasAdded;
use Inowas\Common\Id\ModflowId;
use Inowas\Modflow\Projection\ModelScenarioList\ModelDetailsFinder;
use Inowas\Modflow\Projection\Table;

class BoundaryListProjector extends AbstractDoctrineConnectionProjector
{

    /** @var  GeoTools */
    protected $geoTools;

    /** @var  ModelDetailsFinder */
    protected $modelDetailsFinder;

    public function __construct(Connection $connection, GeoTools $geoTools, ModelDetailsFinder $modelDetailsFinder) {

        $this->geoTools = $geoTools;
        $this->modelDetailsFinder = $modelDetailsFinder;

        parent::__construct($connection);

        $this->schema = new Schema();
        $table = $this->schema->createTable(Table::BOUNDARIES);
        $table->addColumn('id', 'integer', array("unsigned" => true, "autoincrement" => true));
        $table->addColumn('model_id', 'string', ['length' => 36]);
        $table->addColumn('boundary_id', 'string', ['length' => 36]);
        $table->addColumn('type', 'string', ['length' => 255]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('metadata', 'text', ['notnull' => false]);
        $table->addColumn('geometry', 'text', ['notnull' => false]);
        $table->addColumn('active_cells', 'text', ['notnull' => false]);
        $table->addColumn('boundary', 'text', ['notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addIndex(array('model_id'));
    }

    public function onModflowScenarioWasAdded(ModflowScenarioWasAdded $event): void
    {
        $sql = sprintf("SELECT * FROM %s WHERE model_id = ?", Table::BOUNDARIES);
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(1, $event->baseModelId()->toString());
        $stmt->execute();
        $boundaries = $stmt->fetchAll();

        foreach ($boundaries as $boundary){
            $this->insertBoundary(
                $event->scenarioId(),
                BoundaryId::fromString($boundary['boundary_id']),
                BoundaryName::fromString($boundary['name']),
                $boundary['geometry'],
                $boundary['type'],
                $boundary['metadata'],
                $boundary['active_cells'],
                $boundary['boundary']
            );
        }
    }

    public function onBoundaryWasAdded(BoundaryWasAdded $event): void
    {
        $gridSize = $this->modelDetailsFinder->findGridSizeByBaseModelId($event->modflowId());
        $boundingBox = $this->modelDetailsFinder->findBoundingBoxByBaseModelId($event->modflowId());
        $activeCells = $this->calculateActiveCells($event->boundary(), $boundingBox, $gridSize);

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
            'boundary_id' => $event->boundaryId()->toString()
        ));
    }

    public function onBoundaryWasUpdated(BoundaryWasUpdated $event): void
    {
        $gridSize = $this->modelDetailsFinder->findGridSizeByBaseModelId($event->baseModelId());
        $boundingBox = $this->modelDetailsFinder->findBoundingBoxByBaseModelId($event->baseModelId());
        $activeCells = $this->calculateActiveCells($event->boundary(), $boundingBox, $gridSize);

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
            'model_id' => $event->modelId()->toString()
        ));
    }

    public function onBoundaryWasAddedToScenario(BoundaryWasAddedToScenario $event): void
    {
        $gridSize = $this->modelDetailsFinder->findGridSizeByBaseModelId($event->modflowId());
        $boundingBox = $this->modelDetailsFinder->findBoundingBoxByBaseModelId($event->modflowId());
        $activeCells = $this->calculateActiveCells($event->boundary(), $boundingBox, $gridSize);

        /** @var ModflowBoundary $boundary */
        $boundary = $event->boundary();
        $boundary = $boundary->setActiveCells($activeCells);

        $this->insertBoundary(
            $event->scenarioId(),
            $event->boundary()->boundaryId(),
            $event->boundary()->name(),
            $event->boundary()->geometry()->toJson(),
            $event->boundary()->type(),
            json_encode($event->boundary()->metadata()),
            json_encode($activeCells->toArray()),
            base64_encode(serialize($boundary))
        );
    }

    public function onBoundaryWasRemovedFromScenario(BoundaryWasRemovedFromScenario $event): void
    {
        $this->connection->delete(Table::BOUNDARIES, array(
            'boundary_id' => $event->boundaryId()->toString(),
            'model_id' => $event->scenarioId()->toString(),
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

    private function calculateActiveCells(ModflowBoundary $boundary, BoundingBox $boundingBox, GridSize $gridSize): ActiveCells
    {
        return $this->geoTools->calculateActiveCells($boundary, $boundingBox, $gridSize);
    }
}
