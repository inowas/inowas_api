<?php

declare(strict_types=1);

namespace Inowas\ScenarioAnalysis\Infrastructure\Projection;


use Doctrine\DBAL\Connection;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisId;

class ScenarioAnalysisFinder
{
    /** @var Connection $connection */
    protected $connection;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
        $this->connection->setFetchMode(\PDO::FETCH_OBJ);
    }

    public function findScenarioAnalysis(ScenarioAnalysisId $scenarioAnalysisId): array
    {
        $row = $this->connection->fetchAssoc(
            sprintf('SELECT * FROM %s WHERE scenario_analysis_id = :scenario_analysis_id', Table::SCENARIO_ANALYSIS_LIST),
            ['scenario_analysis_id' => $scenarioAnalysisId->toString()]
        );

        return $row;
    }

    public function findAll(): array
    {
        return $this->connection->fetchAll(sprintf('SELECT * FROM %s', Table::SCENARIO_ANALYSIS_LIST));
    }
}
