<?php

namespace Inowas\Modflow\Projection\BoundaryList;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Modflow\Model\BoundaryId;
use Inowas\Modflow\Model\BoundaryName;
use Inowas\Modflow\Model\Event\BoundaryWasAdded;
use Inowas\Modflow\Model\Event\BoundaryWasAddedToScenario;
use Inowas\Modflow\Model\Event\BoundaryWasRemoved;
use Inowas\Modflow\Model\Event\BoundaryWasRemovedFromScenario;
use Inowas\Modflow\Model\Event\ModflowModelBoundaryWasUpdated;
use Inowas\Modflow\Model\Event\ModflowModelWasCreated;
use Inowas\Modflow\Model\Event\ModflowScenarioWasAdded;
use Inowas\Modflow\Model\ModflowId;
use Inowas\Modflow\Projection\ProjectionInterface;
use Inowas\Modflow\Projection\Table;

class BoundaryListProjector implements ProjectionInterface
{

    /** @var Connection $connection */
    protected $connection;

    /** @var Schema $schema */
    protected $schema;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);

        $this->schema = new Schema();
        $table = $this->schema->createTable(Table::BOUNDARIES);
        $table->addColumn('id', 'integer', array("unsigned" => true, "autoincrement" => true));
        $table->addColumn('model_id', 'string', ['length' => 36]);
        $table->addColumn('boundary_id', 'string', ['length' => 36]);
        $table->addColumn('type', 'string', ['length' => 255]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('metadata', 'text', ['notnull' => false]);
        $table->addColumn('geometry', 'text', ['notnull' => false]);
        $table->addColumn('data', 'text', ['notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addIndex(array('model_id'));
    }

    public function onModflowModelWasCreated(ModflowModelWasCreated $event)
    {

    }

    public function onModflowScenarioWasAdded(ModflowScenarioWasAdded $event)
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
                $boundary['data']
            );
        }
    }

    public function onBoundaryWasAdded(BoundaryWasAdded $event)
    {
        $this->insertBoundary(
            $event->modflowId(),
            $event->boundary()->boundaryId(),
            $event->boundary()->name(),
            $event->boundary()->geometry()->toJson(),
            $event->boundary()->type(),
            json_encode($event->boundary()->metadata()),
            $event->boundary()->dataToJson()
        );
    }

    public function onBoundaryWasRemoved(BoundaryWasRemoved $event)
    {
        $this->connection->delete(Table::BOUNDARIES, array(
            'boundary_id' => $event->boundaryId()->toString()
        ));
    }

    public function onModflowModelBoundaryWasUpdated(ModflowModelBoundaryWasUpdated $event)
    {
        $this->connection->delete(Table::BOUNDARIES, array(
            'boundary_id' => $event->boundary()->boundaryId()->toString(),
            'model_id' => $event->modflowId()->toString()
        ));
    }

    public function onBoundaryWasAddedToScenario(BoundaryWasAddedToScenario $event)
    {
        $this->insertBoundary(
            $event->scenarioId(),
            $event->boundary()->boundaryId(),
            $event->boundary()->name(),
            $event->boundary()->geometry()->toJson(),
            $event->boundary()->type(),
            json_encode($event->boundary()->metadata()),
            $event->boundary()->dataToJson()
        );
    }

    public function onBoundaryWasRemovedFromScenario(BoundaryWasRemovedFromScenario $event)
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
        string $data
    )
    {
        $this->connection->insert(Table::BOUNDARIES, array(
            'model_id' => $modelId->toString(),
            'boundary_id' => $boundaryId->toString(),
            'name' => $boundaryName->toString(),
            'geometry' => $boundaryGeometry,
            'type' => $boundaryType,
            'metadata' => $metadata,
            'data' => $data
        ));
    }

    public function createTable(): void
    {
        $queryArray = $this->schema->toSql($this->connection->getDatabasePlatform());
        $this->executeQueryArray($queryArray);

    }

    public function dropTable(): void
    {
        try {
            $queryArray = $this->schema->toDropSql($this->connection->getDatabasePlatform());
            $this->executeQueryArray($queryArray);
        } catch (TableNotFoundException $e) {}
    }

    public function truncateTable(): void
    {
        $this->dropTable();
        $this->createTable();
    }

    public function reset(): void
    {
        $this->truncateTable();
    }


    private function executeQueryArray(array $queries)
    {
        foreach ($queries as $query){
            $this->connection->executeQuery($query);
        }
    }
}
