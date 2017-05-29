<?php

declare(strict_types=1);

namespace Inowas\ScenarioAnalysis\Infrastructure\Projection;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\ModflowModel\Infrastructure\Projection\ModelList\ModelFinder;
use Inowas\ScenarioAnalysis\Model\Event\ScenarioAnalysisWasCreated;
use Inowas\ScenarioAnalysis\Model\Event\ScenarioWasCreated;
use Inowas\ScenarioAnalysis\Model\Event\ScenarioWasRemoved;

class ScenarioListProjector extends AbstractDoctrineConnectionProjector
{

    /** @var  ModelFinder */
    private $modelFinder;

    public function __construct(Connection $connection, ModelFinder $modelFinder)
    {
        $this->modelFinder = $modelFinder;

        parent::__construct($connection);

        $this->schema = new Schema();
        $table = $this->schema->createTable(Table::SCENARIO_LIST);
        $table->addColumn('scenario_id', 'string', ['length' => 36]);
        $table->addColumn('base_model_id', 'string', ['length' => 36]);
        $table->addColumn('scenario_analysis_id', 'string', ['length' => 36]);
        $table->addColumn('user_id', 'string', ['length' => 36]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('description', 'string', ['length' => 255]);
        $table->addColumn('is_base_model', 'boolean', ['default' => false]);
        $table->addColumn('is_scenario', 'boolean', ['default' => false]);
        $table->addColumn('created_at', 'string', ['length' => 255, 'notnull' => false]);
        $table->setPrimaryKey(['scenario_id']);
        $table->addIndex(array('scenario_analysis_id', 'base_model_id'));
    }

    public function onScenarioAnalysisWasCreated(ScenarioAnalysisWasCreated $event)
    {

        $baseModelName = $this->modelFinder->getModelNameByModelId($event->baseModelId());
        $baseModelDescription = $this->modelFinder->getModelDescriptionByModelId($event->baseModelId());

        $this->connection->insert(Table::SCENARIO_LIST, array(
            'scenario_id' => $event->baseModelId()->toString(),
            'base_model_id' => $event->baseModelId()->toString(),
            'scenario_analysis_id' => $event->scenarioAnalysisId()->toString(),
            'user_id' => $event->userId()->toString(),
            'name' => $baseModelName->toString(),
            'description' => $baseModelDescription->toString(),
            'is_base_model' => 1,
            'is_scenario' => 0,
            'created_at' => date_format($event->createdAt(), DATE_ATOM),
        ));
    }

    public function onScenarioWasCreated(ScenarioWasCreated $event): void
    {
        $this->connection->insert(Table::SCENARIO_LIST, array(
            'scenario_id' => $event->scenarioId()->toString(),
            'base_model_id' => $event->baseModelId()->toString(),
            'scenario_analysis_id' => $event->scenarioAnalysisId()->toString(),
            'user_id' => $event->userId()->toString(),
            'name' => $event->name()->toString(),
            'description' => $event->description()->toString(),
            'is_base_model' => 0,
            'is_scenario' => 1,
            'created_at' => date_format($event->createdAt(), DATE_ATOM),
        ));
    }

    public function onScenarioWasRemoved(ScenarioWasRemoved $event): void
    {
        $this->connection->delete(Table::SCENARIO_LIST,
            array('scenario_id' => $event->scenarioId()->toString(), 'scenario_analysis_id' => $event->scenarioAnalysisId()->toString())
        );
    }
}
