<?php

declare(strict_types=1);

namespace Inowas\Modflow\Projection\BoundaryList;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Boundaries\ObservationPoint;
use Inowas\Common\Boundaries\ObservationPointName;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ObservationPointId;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\Modflow\Model\Event\BoundaryWasAdded;
use Inowas\Common\Id\ModflowId;
use Inowas\Modflow\Projection\Table;

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
