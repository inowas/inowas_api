<?php

declare(strict_types=1);

namespace Inowas\Modflow\Projection\ModelScenarioList;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\Modflow\Model\Event\BoundaryWasAdded;
use Inowas\Modflow\Model\Event\ModflowModelBoundaryWasUpdated;
use Inowas\Modflow\Model\Event\ModflowModelBoundingBoxWasChanged;
use Inowas\Modflow\Model\Event\ModflowModelDescriptionWasChanged;
use Inowas\Modflow\Model\Event\ModflowModelGridSizeWasChanged;
use Inowas\Modflow\Model\Event\ModflowModelNameWasChanged;
use Inowas\Modflow\Model\Event\ModflowModelWasCreated;
use Inowas\Modflow\Model\Event\ModflowScenarioDescriptionWasChanged;
use Inowas\Modflow\Model\Event\ModflowScenarioNameWasChanged;
use Inowas\Modflow\Model\Event\ModflowScenarioWasAdded;
use Inowas\Modflow\Projection\ProjectionInterface;
use Inowas\Modflow\Projection\Table;

class ModelScenarioListProjector extends AbstractDoctrineConnectionProjector
{

    public function __construct(Connection $connection)
    {

        parent::__construct($connection);

        $this->schema = new Schema();
        $table = $this->schema->createTable(Table::MODEL_SCENARIO_LIST);
        $table->addColumn('id', 'integer', array("unsigned" => true, "autoincrement" => true));
        $table->addColumn('user_id', 'string', ['length' => 36]);
        $table->addColumn('base_model_id', 'string', ['length' => 36]);
        $table->addColumn('scenario_id', 'string', ['length' => 36]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('description', 'string', ['length' => 255]);
        $table->addColumn('area', 'text', ['notnull' => false]);
        $table->addColumn('grid_size', 'text', ['notnull' => false]);
        $table->addColumn('bounding_box', 'text', ['notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addIndex(array('base_model_id'));
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

    public function onBoundaryWasAdded(BoundaryWasAdded $event): void
    {
        $boundary = $event->boundary();
        if ($boundary->type() == 'area') {
            $this->connection->update(Table::MODEL_SCENARIO_LIST,
                array('area' => json_encode($boundary->geometry()->toJson())),
                array('base_model_id' => $event->modflowId()->toString())
            );
        }
    }

    public function onModflowModelBoundaryWasUpdated(ModflowModelBoundaryWasUpdated $event): void
    {
        $boundary = $event->boundary();
        if ($boundary->type() == 'area') {
            $this->connection->update(Table::MODEL_SCENARIO_LIST,
                array('area' => json_encode($boundary->geometry()->toJson())),
                array('base_model_id' => $event->modflowId()->toString())
            );
        }
    }

    public function onModflowModelBoundingBoxWasChanged(ModflowModelBoundingBoxWasChanged $event): void
    {
        $this->connection->update(Table::MODEL_SCENARIO_LIST,
            array('bounding_box' => json_encode($event->boundingBox())),
            array('base_model_id' => $event->modflowModelId()->toString())
        );
    }

    public function onModflowModelGridSizeWasChanged(ModflowModelGridSizeWasChanged $event): void
    {
        $this->connection->update(Table::MODEL_SCENARIO_LIST,
            array('grid_size' => json_encode($event->gridSize())),
            array('base_model_id' => $event->modflowModelId()->toString())
        );
    }
}
