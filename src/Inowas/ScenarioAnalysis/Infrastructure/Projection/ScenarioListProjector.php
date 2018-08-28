<?php

declare(strict_types=1);

namespace Inowas\ScenarioAnalysis\Infrastructure\Projection;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\ScenarioAnalysis\Model\Event\ScenarioAnalysisWasCloned;
use Inowas\ScenarioAnalysis\Model\Event\ScenarioAnalysisWasCreated;
use Inowas\ScenarioAnalysis\Model\Event\ScenarioAnalysisWasDeleted;
use Inowas\ScenarioAnalysis\Model\Event\ScenarioWasCreated;
use Inowas\ScenarioAnalysis\Model\Event\ScenarioWasDeleted;

class ScenarioListProjector extends AbstractDoctrineConnectionProjector
{

    public function __construct(Connection $connection)
    {
        parent::__construct($connection);

        $schema = new Schema();
        $table = $schema->createTable(Table::SCENARIO_LIST);
        $table->addColumn('id', 'integer')->setAutoincrement(true);
        $table->addColumn('scenario_id', 'string', ['length' => 36]);
        $table->addColumn('base_model_id', 'string', ['length' => 36]);
        $table->addColumn('scenario_analysis_id', 'string', ['length' => 36]);
        $table->addColumn('user_id', 'string', ['length' => 36]);
        $table->addColumn('is_base_model', 'boolean', ['default' => false]);
        $table->addColumn('is_scenario', 'boolean', ['default' => false]);
        $table->addColumn('created_at', 'string', ['length' => 255, 'notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addIndex(array('scenario_analysis_id'));
        $table->addIndex(array('base_model_id'));
        $table->addIndex(array('scenario_id'));
        $this->addSchema($schema);
    }

    public function onScenarioAnalysisWasCreated(ScenarioAnalysisWasCreated $event): void
    {
        $this->connection->insert(Table::SCENARIO_LIST, array(
            'scenario_id' => $event->baseModelId()->toString(),
            'base_model_id' => $event->baseModelId()->toString(),
            'scenario_analysis_id' => $event->scenarioAnalysisId()->toString(),
            'user_id' => $event->userId()->toString(),
            'is_base_model' => 1,
            'is_scenario' => 0,
            'created_at' => date_format($event->createdAt(), DATE_ATOM),
        ));
    }

    public function onScenarioAnalysisWasCloned(ScenarioAnalysisWasCloned $event): void
    {

        $this->connection->insert(Table::SCENARIO_LIST, array(
            'scenario_id' => $event->baseModelId()->toString(),
            'base_model_id' => $event->baseModelId()->toString(),
            'scenario_analysis_id' => $event->scenarioAnalysisId()->toString(),
            'user_id' => $event->userId()->toString(),
            'is_base_model' => 1,
            'is_scenario' => 0,
            'created_at' => date_format($event->createdAt(), DATE_ATOM),
        ));

        $rows = $this->connection->fetchAll(
            sprintf('SELECT * FROM %s WHERE scenario_analysis_id=:scenario_analysis_id AND is_scenario = TRUE ORDER BY created_at', Table::SCENARIO_LIST),
            ['scenario_analysis_id' => $event->fromScenarioAnalysisId()->toString()]
        );

        if (false === $rows) {
            return;
        }

        foreach ($rows as $key => $row) {
            $this->connection->insert(Table::SCENARIO_LIST, array(
                'scenario_id' => $event->scenarios()[$key],
                'base_model_id' => $event->baseModelId()->toString(),
                'scenario_analysis_id' => $event->scenarioAnalysisId()->toString(),
                'user_id' => $event->userId()->toString(),
                'is_base_model' => 0,
                'is_scenario' => 1,
                'created_at' => date_format($event->createdAt(), DATE_ATOM),
            ));
        }
    }

    /**
     * @param ScenarioAnalysisWasDeleted $event
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function onScenarioAnalysisWasDeleted(ScenarioAnalysisWasDeleted $event): void
    {
        $this->connection->delete(
            Table::SCENARIO_LIST,
            ['scenario_analysis_id' => $event->scenarioAnalysisId()->toString()]
        );
    }

    public function onScenarioWasCreated(ScenarioWasCreated $event): void
    {
        $this->connection->insert(Table::SCENARIO_LIST, array(
            'scenario_id' => $event->scenarioId()->toString(),
            'base_model_id' => $event->baseModelId()->toString(),
            'scenario_analysis_id' => $event->scenarioAnalysisId()->toString(),
            'user_id' => $event->userId()->toString(),
            'is_base_model' => 0,
            'is_scenario' => 1,
            'created_at' => date_format($event->createdAt(), DATE_ATOM),
        ));
    }

    /**
     * @param ScenarioWasDeleted $event
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function onScenarioWasDeleted(ScenarioWasDeleted $event): void
    {
        $this->connection->delete(Table::SCENARIO_LIST,
            [
                'scenario_id' => $event->scenarioId()->toString(),
                'scenario_analysis_id' => $event->scenarioAnalysisId()->toString(),
                'user_id' => $event->userId()->toString()
            ]
        );
    }
}
