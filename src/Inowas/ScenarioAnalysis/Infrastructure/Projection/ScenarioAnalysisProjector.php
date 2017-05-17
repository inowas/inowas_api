<?php

declare(strict_types=1);

namespace Inowas\ScenarioAnalysis\Infrastructure\Projection;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\ModflowModel\Infrastructure\Projection\ModelList\ModelFinder;
use Inowas\ScenarioAnalysis\Model\Event\ScenarioAnalysisDescriptionWasChanged;
use Inowas\ScenarioAnalysis\Model\Event\ScenarioAnalysisNameWasChanged;
use Inowas\ScenarioAnalysis\Model\Event\ScenarioAnalysisWasCreated;
use Inowas\ScenarioAnalysis\Model\Event\ScenarioWasAdded;
use Inowas\ScenarioAnalysis\Model\Event\ScenarioWasRemoved;

class ScenarioAnalysisProjector extends AbstractDoctrineConnectionProjector
{

    /** @var  ModelFinder */
    private $modelFinder;

    public function __construct(Connection $connection, ModelFinder $modelFinder)
    {

        $this->modelFinder = $modelFinder;

        parent::__construct($connection);

        $this->schema = new Schema();
        $table = $this->schema->createTable(Table::SCENARIO_ANALYSIS_LIST);
        $table->addColumn('scenario_analysis_id', 'string', ['length' => 36]);
        $table->addColumn('user_id', 'string', ['length' => 36]);
        $table->addColumn('base_model_id', 'string', ['length' => 36]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('description', 'string', ['length' => 255]);
        $table->addColumn('area', 'text');
        $table->addColumn('grid_size', 'text');
        $table->addColumn('bounding_box', 'text');
        $table->addColumn('scenarios', 'text');
        $table->setPrimaryKey(['scenario_analysis_id']);
        $table->addIndex(array('base_model_id'));
    }

    public function onScenarioAnalysisWasCreated(ScenarioAnalysisWasCreated $event)
    {
        $areaGeometry = $this->modelFinder->getAreaPolygonByModflowModelId($event->baseModelId());
        $boundingBox = $this->modelFinder->getBoundingBoxByModflowModelId($event->baseModelId());
        $gridSize = $this->modelFinder->getGridSizeByModflowModelId($event->baseModelId());

        $this->connection->insert(Table::SCENARIO_ANALYSIS_LIST, array(
            'scenario_analysis_id' => $event->scenarioAnalysisId()->toString(),
            'user_id' => $event->userId()->toString(),
            'base_model_id' => $event->baseModelId()->toString(),
            'name' => '',
            'description' => '',
            'area' => $areaGeometry->toJson(),
            'grid_size' => json_encode($gridSize),
            'bounding_box' => json_encode($boundingBox),
            'scenarios' => json_encode([])
        ));
    }

    public function onScenarioAnalysisNameWasChanged(ScenarioAnalysisNameWasChanged $event)
    {
        $this->connection->update(Table::SCENARIO_ANALYSIS_LIST,
            array('name' => $event->name()->toString()),
            array('scenario_analysis_id' => $event->scenarioAnalysisId()->toString())
        );
    }

    public function onScenarioAnalysisDescriptionWasChanged(ScenarioAnalysisDescriptionWasChanged $event)
    {
        $this->connection->update(Table::SCENARIO_ANALYSIS_LIST,
            array('description' => $event->description()->toString()),
            array('scenario_analysis_id' => $event->scenarioAnalysisId()->toString())
        );
    }

    public function onScenarioWasAdded(ScenarioWasAdded $event): void
    {
        $result =  $this->connection->fetchAssoc(
            sprintf('SELECT scenarios FROM %s WHERE scenario_analysis_id = :scenario_analysis_id', Table::SCENARIO_ANALYSIS_LIST),
            ['scenario_analysis_id' => $event->scenarioAnalysisId()->toString()]
        );

        $scenarios = json_decode($result['scenarios']);
        $scenarios[] = $event->scenarioId()->toString();

        $this->connection->update(Table::SCENARIO_ANALYSIS_LIST,
            array('scenarios' => json_encode($scenarios)),
            array('scenario_analysis_id' => $event->scenarioAnalysisId()->toString())
        );
    }

    public function onScenarioWasRemoved(ScenarioWasRemoved $event): void
    {
        $result =  $this->connection->fetchAssoc(
            sprintf('SELECT scenarios FROM %s WHERE scenario_analysis_id = :scenario_analysis_id', Table::SCENARIO_ANALYSIS_LIST),
            ['scenario_analysis_id' => $event->scenarioAnalysisId()->toString()]
        );

        $scenarios = json_decode($result['scenarios']);
        $scenarios = array_diff($scenarios, [$event->scenarioId()->toString()]);

        $this->connection->update(Table::SCENARIO_ANALYSIS_LIST,
            array('scenarios' => json_encode($scenarios)),
            array('scenario_analysis_id' => $event->scenarioAnalysisId()->toString())
        );
    }
}
