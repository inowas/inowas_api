<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Infrastructure\Projection\BoundaryList;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Boundaries\ObservationPoint;
use Inowas\Common\Boundaries\ObservationPointName;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ObservationPointId;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\ModflowModel\Model\Event\BoundaryWasAdded;
use Inowas\Common\Id\ModflowId;
use Inowas\ModflowModel\Model\Event\BoundaryWasAddedToScenario;
use Inowas\ModflowModel\Model\Event\BoundaryWasRemoved;
use Inowas\ModflowModel\Model\Event\BoundaryWasRemovedFromScenario;
use Inowas\ModflowModel\Model\Event\ModflowScenarioWasAdded;
use Inowas\ModflowModel\Infrastructure\Projection\Table;

class BoundaryValuesProjector extends AbstractDoctrineConnectionProjector
{

    public function __construct(Connection $connection) {

        parent::__construct($connection);

        $this->schema = new Schema();
        $table = $this->schema->createTable(Table::BOUNDARY_VALUES);
        $table->addColumn('model_id', 'string', ['length' => 36]);
        $table->addColumn('boundary_id', 'string', ['length' => 36]);
        $table->addColumn('boundary_type', 'string', ['length' => 255]);
        $table->addColumn('observation_point_id', 'string', ['length' => 36]);
        $table->addColumn('observation_point_name', 'string', ['length' => 255]);
        $table->addColumn('observation_point_geometry', 'text', ['notnull' => false]);
        $table->addColumn('data', 'text', ['notnull' => false]);
        $table->addIndex(array('model_id'));
    }

    public function onBoundaryWasAdded(BoundaryWasAdded $event): void
    {
        $boundary = $event->boundary();
        /** @var ObservationPoint $observationPoint */
        foreach ($boundary->observationPoints() as $observationPoint){
            $this->insertBoundary(
                $event->modflowId(),
                $boundary->boundaryId(),
                $boundary->type(),
                $observationPoint->id(),
                $observationPoint->name(),
                $observationPoint->geometryJson(),
                json_encode($observationPoint->dateTimeValues())
            );
        }
    }

    public function onBoundaryWasRemoved(BoundaryWasRemoved $event): void
    {
        $this->connection->delete(Table::BOUNDARY_VALUES, array(
            'model_id' => $event->modflowId()->toString(),
            'boundary_id' => $event->boundaryId()->toString()
        ));
    }

    public function onModflowScenarioWasAdded(ModflowScenarioWasAdded $event): void
    {
        $sql = sprintf("SELECT * FROM %s WHERE model_id = ?", Table::BOUNDARY_VALUES);
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(1, $event->baseModelId()->toString());
        $stmt->execute();
        $boundaries = $stmt->fetchAll();

        foreach ($boundaries as $boundary){
            $this->insertBoundary(
                $event->scenarioId(),
                BoundaryId::fromString($boundary['boundary_id']),
                $boundary['boundary_type'],
                ObservationPointId::fromString($boundary['observation_point_id']),
                ObservationPointName::fromString($boundary['observation_point_name']),
                $boundary['observation_point_geometry'],
                $boundary['data']
            );
        }
    }

    public function onBoundaryWasAddedToScenario(BoundaryWasAddedToScenario $event): void
    {
        $boundary = $event->boundary();
        /** @var ObservationPoint $observationPoint */
        foreach ($boundary->observationPoints() as $observationPoint){
            $this->insertBoundary(
                $event->scenarioId(),
                $boundary->boundaryId(),
                $boundary->type(),
                $observationPoint->id(),
                $observationPoint->name(),
                $observationPoint->geometryJson(),
                json_encode($observationPoint->dateTimeValues())
            );
        }
    }

    public function onBoundaryWasRemovedFromScenario(BoundaryWasRemovedFromScenario $event): void
    {
        $this->connection->delete(Table::BOUNDARY_VALUES, array(
            'model_id' => $event->scenarioId()->toString(),
            'boundary_id' => $event->boundaryId()->toString()
        ));
    }

    private function insertBoundary(
        ModflowId $modelId,
        BoundaryId $boundaryId,
        string $boundaryType,
        ObservationPointId $observationPointId,
        ObservationPointName $observationPointName,
        string $observationPointGeometry,
        string $data
    ): void
    {
        $this->connection->insert(Table::BOUNDARY_VALUES, array(
            'model_id' => $modelId->toString(),
            'boundary_id' => $boundaryId->toString(),
            'boundary_type' => $boundaryType,
            'observation_point_id' => $observationPointId->toString(),
            'observation_point_name' => $observationPointName->toString(),
            'observation_point_geometry' => $observationPointGeometry,
            'data' => $data
        ));
    }
}
