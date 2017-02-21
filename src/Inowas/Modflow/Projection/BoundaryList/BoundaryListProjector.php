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
use Inowas\Modflow\Model\Event\ModflowModelWasCreated;
use Inowas\Modflow\Model\Event\ModflowScenarioWasAdded;
use Inowas\Modflow\Model\ModflowId;
use Inowas\Modflow\Model\UserId;
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
        $table->addColumn('boundary_id', 'string', ['length' => 36]);
        $table->addColumn('type', 'string', ['length' => 255]);
        $table->addColumn('user_id', 'string', ['length' => 36]);
        $table->addColumn('base_model_id', 'string', ['length' => 36]);
        $table->addColumn('scenario_id', 'string', ['length' => 36]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('metadata', 'text');
        $table->addColumn('data', 'text', ['nullable' => true]);
        $table->addColumn('geometry', 'text');
        $table->setPrimaryKey(['id']);
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

    public function onModflowModelWasCreated(ModflowModelWasCreated $event)
    {

    }

    public function onModflowScenarioWasAdded(ModflowScenarioWasAdded $event)
    {
        $sql = sprintf("SELECT * FROM %s WHERE base_model_id = ? AND user_id = ? AND scenario_id = ?", Table::BOUNDARIES);
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(1, $event->baseModelId()->toString());
        $stmt->bindValue(2, $event->userId()->toString());
        $stmt->bindValue(3, '');
        $stmt->execute();
        $boundaries = $stmt->fetchAll();

        foreach ($boundaries as $boundary){
            $this->insertBoundary(
                BoundaryId::fromString($boundary['boundary_id']),
                UserId::fromString($boundary['user_id']),
                ModflowId::fromString($boundary['base_model_id']),
                $event->scenarioId()->toString(),
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
            $event->boundary()->boundaryId(),
            $event->userId(),
            $event->modflowId(),
            '',
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
            'boundary_id' => $event->boundaryId()->toString(),
            'user_id' => $event->userId()->toString(),
            'base_model_id' => $event->modflowId()->toString()
        ));
    }

    public function onBoundaryWasAddedToScenario(BoundaryWasAddedToScenario $event)
    {
        $this->insertBoundary(
            $event->boundary()->boundaryId(),
            $event->userId(),
            $event->modflowId(),
            $event->scenarioId()->toString(),
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
            'user_id' => $event->userId()->toString(),
            'base_model_id' => $event->modflowId()->toString(),
            'scenario_id' => $event->scenarioId()->toString()
        ));
    }

    private function insertBoundary(
        BoundaryId $boundaryId,
        UserId $userId,
        ModflowId $baseModelId,
        string $scenarioId,
        BoundaryName $boundaryName,
        string $boundaryGeometry,
        string $boundaryType,
        string $metadata,
        string $data
    )
    {
        $this->connection->insert(Table::BOUNDARIES, array(
            'boundary_id' => $boundaryId->toString(),
            'user_id' => $userId->toString(),
            'base_model_id' => $baseModelId->toString(),
            'scenario_id' => $scenarioId,
            'name' => $boundaryName->toString(),
            'geometry' => $boundaryGeometry,
            'type' => $boundaryType,
            'metadata' => $metadata,
            'data' => $data
        ));
    }
}
