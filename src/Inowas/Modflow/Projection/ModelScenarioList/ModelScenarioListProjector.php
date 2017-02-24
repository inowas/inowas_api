<?php

namespace Inowas\Modflow\Projection\ModelScenarioList;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Modflow\Model\Event\BoundaryWasAdded;
use Inowas\Modflow\Model\Event\ModflowCalculationWasCreated;
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

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;

        $this->schema = new Schema();
        $table = $this->schema->createTable(Table::MODEL_SCENARIO_LIST);
        $table->addColumn('id', 'integer', array("unsigned" => true, "autoincrement" => true));
        $table->addColumn('user_id', 'string', ['length' => 36]);
        $table->addColumn('base_model_id', 'string', ['length' => 36]);
        $table->addColumn('scenario_id', 'string', ['length' => 36]);
        $table->addColumn('calculation_id', 'string', ['length' => 36, 'notnull' => false]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('description', 'string', ['length' => 255]);
        $table->addColumn('area_geometry', 'text', ['notnull' => false]);
        $table->addColumn('grid_size', 'text', ['notnull' => false]);
        $table->addColumn('bounding_box', 'text', ['notnull' => false]);
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
        } catch (TableNotFoundException $e) {
        }
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
        foreach ($queries as $query) {
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
        $sql = sprintf("SELECT area_geometry, grid_size FROM %s WHERE base_model_id = ? AND user_id = ?", Table::MODEL_SCENARIO_LIST);
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(1, $event->baseModelId()->toString());
        $stmt->bindValue(2, $event->userId()->toString());
        $stmt->execute();
        $result = $stmt->fetchAll()[0];
        $area_geometry = $result['area_geometry'];
        $grid_size = $result['grid_size'];

        $this->connection->insert(Table::MODEL_SCENARIO_LIST, array(
            'user_id' => $event->userId()->toString(),
            'base_model_id' => $event->baseModelId()->toString(),
            'scenario_id' => $event->scenarioId()->toString(),
            'name' => '',
            'description' => '',
            'area_geometry' => $area_geometry,
            'grid_size' => $grid_size
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

    public function onModflowCalculationWasCreated(ModflowCalculationWasCreated $event){
        $this->connection->update(Table::MODEL_SCENARIO_LIST,
            array('calculation_id' => $event->calculationId()->toString()),
            array(
                'base_model_id' => $event->modflowModelId()->toString(),
                'scenario_id' => ''
            )
        );

        $this->connection->update(Table::MODEL_SCENARIO_LIST,
            array('calculation_id' => $event->calculationId()->toString()),
            array('scenario_id' => $event->modflowModelId()->toString())
        );
    }

    public function onBoundaryWasAdded(BoundaryWasAdded $event){
        $boundary = $event->boundary();
        if ($boundary->type() == 'area') {
            $this->connection->update(Table::MODEL_SCENARIO_LIST,
                array('area_geometry' => $boundary->geometry()->toJson()),
                array('base_model_id' => $event->modflowId()->toString())
            );
        }
    }
}
