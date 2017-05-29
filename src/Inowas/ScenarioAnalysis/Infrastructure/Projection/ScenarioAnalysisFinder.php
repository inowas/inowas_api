<?php

declare(strict_types=1);

namespace Inowas\ScenarioAnalysis\Infrastructure\Projection;


use Doctrine\DBAL\Connection;
use Inowas\Common\Id\UserId;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisId;

class ScenarioAnalysisFinder
{
    /** @var Connection $connection */
    protected $connection;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);
    }

    public function findScenarioAnalysesByUserId(UserId $userId): array
    {
        $results = $this->connection->fetchAll(
            sprintf('SELECT scenario_analysis_id as id, user_id, user_name, base_model_id, name, description, area as geometry, grid_size, bounding_box, scenarios as scenario_ids, created_at, public FROM %s WHERE user_id = :user_id', Table::SCENARIO_ANALYSIS_LIST),
            ['user_id' => $userId->toString()]
        );

        if ($results === false) {
            $results = [];
        }

        foreach ($results as $key => $row){
            $results[$key]['geometry'] = json_decode($row['geometry'], true);
            $results[$key]['grid_size'] = json_decode($row['grid_size'], true);
            $results[$key]['bounding_box'] = json_decode($row['bounding_box'], true);
            $results[$key]['scenario_ids'] = json_decode($row['scenario_ids'], true);
        }

        return $results;
    }

    public function findPublicScenarioAnalyses(): array
    {
        $results = $this->connection->fetchAll(
            sprintf('SELECT scenario_analysis_id as id, user_id, user_name, base_model_id, name, description, area as geometry, grid_size, bounding_box, scenarios as scenario_ids, created_at, public FROM %s WHERE public = true', Table::SCENARIO_ANALYSIS_LIST)
        );

        if ($results === false) {
            $results = [];
        }

        foreach ($results as $key => $row){
            $results[$key]['geometry'] = json_decode($row['geometry'], true);
            $results[$key]['grid_size'] = json_decode($row['grid_size'], true);
            $results[$key]['bounding_box'] = json_decode($row['bounding_box'], true);
            $results[$key]['scenario_ids'] = json_decode($row['scenario_ids'], true);
        }

        return $results;
    }

    public function findScenarioAnalysisById(ScenarioAnalysisId $scenarioAnalysisId): ?array
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT scenario_analysis_id as id, user_id, user_name, base_model_id, name, description, area as geometry, grid_size, bounding_box, scenarios as scenario_ids, created_at, public FROM %s WHERE scenario_analysis_id = :scenario_analysis_id', Table::SCENARIO_ANALYSIS_LIST),
            ['scenario_analysis_id' => $scenarioAnalysisId->toString()]
        );

        if ($result === false) {
            return null;
        }

        $result['geometry'] = json_decode($result['geometry'], true);
        $result['grid_size'] = json_decode($result['grid_size'], true);
        $result['bounding_box'] = json_decode($result['bounding_box'], true);
        $result['scenario_ids'] = json_decode($result['scenario_ids'], true);

        return $result;
    }

    public function findAll(): array
    {
        return $this->connection->fetchAll(sprintf('SELECT * FROM %s', Table::SCENARIO_ANALYSIS_LIST));
    }
}
