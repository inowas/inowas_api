<?php

namespace Inowas\Modflow\Projection\ModelScenarioList;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Modflow\Model\Event\ModflowCalculationResultWasAdded;
use Inowas\Modflow\Model\Event\ModflowModelDescriptionWasChanged;
use Inowas\Modflow\Model\Event\ModflowModelNameWasChanged;
use Inowas\Modflow\Model\Event\ModflowModelWasCreated;
use Inowas\Modflow\Model\Event\ModflowScenarioDescriptionWasChanged;
use Inowas\Modflow\Model\Event\ModflowScenarioNameWasChanged;
use Inowas\Modflow\Model\Event\ModflowScenarioWasAdded;
use Inowas\Modflow\Projection\ProjectionInterface;
use Inowas\Modflow\Projection\Table;

class ModelScenarioListProjector implements ProjectionInterface
{

    /** @var Connection $connection */
    protected $connection;

    /** @var Schema $schema */
    protected $schema;

    public function __construct(Connection $connection) {
        $this->connection = $connection;

        $this->schema = new Schema();
        $table = $this->schema->createTable(Table::MODEL_SCENARIO_LIST);
        $table->addColumn('user_id', 'string', ['length' => 36]);
        $table->addColumn('base_model_id', 'string', ['length' => 36]);
        $table->addColumn('scenario_id', 'string', ['length' => 36]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('description', 'string', ['length' => 255]);
        $table->setPrimaryKey(['base_model_id', 'scenario_id']);
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
        $this->connection->insert(Table::MODEL_SCENARIO_LIST, array(
            'user_id' => $event->userId()->toString(),
            'base_model_id' => $event->modflowModelId()->toString(),
            'scenario_id' => '',
            'name' => '',
            'description' => ''
        ));
    }

    public function onModflowModelNameWasChanged(ModflowModelNameWasChanged $event)
    {
        $this->connection->update(Table::MODEL_SCENARIO_LIST,
            array('name' => $event->name()->toString()),
            array('base_model_id' => $event->modflowId()->toString())
        );
    }

    public function onModflowModelDescriptionWasChanged(ModflowModelDescriptionWasChanged $event)
    {
        $this->connection->update(Table::MODEL_SCENARIO_LIST,
            array('description' => $event->description()->toString()),
            array('base_model_id' => $event->modflowModelId()->toString())
        );
    }

    public function onModflowScenarioWasAdded(ModflowScenarioWasAdded $event): void
    {
        $this->connection->insert(Table::MODEL_SCENARIO_LIST, array(
            'user_id' => $event->userId()->toString(),
            'base_model_id' => $event->baseModelId()->toString(),
            'scenario_id' => $event->scenarioId()->toString(),
            'name' => '',
            'description' => ''
        ));
    }

    public function onModflowScenarioNameWasChanged(ModflowScenarioNameWasChanged $event)
    {
        $this->connection->update(Table::MODEL_SCENARIO_LIST,
            array('name' => $event->name()->toString()),
            array(
                'base_model_id' => $event->modflowId()->toString(),
                'scenario_id' => $event->scenarioId()->toString()
            )
        );
    }

    public function onModflowScenarioDescriptionWasChanged(ModflowScenarioDescriptionWasChanged $event)
    {
        $this->connection->update(Table::MODEL_SCENARIO_LIST,
            array('description' => $event->description()->toString()),
            array(
                'base_model_id' => $event->modflowId()->toString(),
                'scenario_id' => $event->scenarioId()->toString()
            )
        );
    }
}
