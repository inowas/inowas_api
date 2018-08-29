<?php

declare(strict_types=1);

namespace Inowas\ScenarioAnalysis\Infrastructure\Projection;

use Doctrine\DBAL\Connection;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\Description;
use Inowas\Common\Modflow\Name;
use Inowas\Common\Status\Visibility;
use Inowas\ModflowModel\Infrastructure\Projection\ModelList\ModelFinder;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisDescription;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisId;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisName;

class ScenarioAnalysisFinder
{
    /** @var Connection $connection */
    protected $connection;

    /** @var ModelFinder */
    protected $modelFinder;

    public function __construct(Connection $connection, ModelFinder $modelFinder)
    {
        $this->connection = $connection;
        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);
        $this->modelFinder = $modelFinder;
    }

    public function scenarioAnalysisExists(ScenarioAnalysisId $id): bool
    {
        $results = $this->connection->fetchAll(
            sprintf('SELECT scenario_analysis_id FROM %s WHERE scenario_analysis_id = :scenario_analysis_id', Table::SCENARIO_ANALYSIS_LIST),
            ['scenario_analysis_id' => $id->toString()]
        );

        return !($results === false);
    }

    public function scenarioAnalysisContainsScenario(ScenarioAnalysisId $id, ModflowId $scenarioId): bool
    {
        $results = $this->connection->fetchAssoc(
            sprintf('SELECT count(*) FROM %s WHERE scenario_analysis_id = :scenario_analysis_id AND scenario_id = :scenario_id', Table::SCENARIO_LIST),
            ['scenario_analysis_id' => $id->toString(), 'scenario_id' => $scenarioId->toString()]
        );

        return $results['count'] > 0;
    }

    public function getScenarioAnalysisName(ScenarioAnalysisId $id): ?ScenarioAnalysisName
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT name FROM %s WHERE scenario_analysis_id = :scenario_analysis_id', Table::SCENARIO_ANALYSIS_LIST),
            ['scenario_analysis_id' => $id->toString()]
        );

        if ($result === false) {
            return null;
        }

        return ScenarioAnalysisName::fromString($result['name']);
    }

    public function getScenarioAnalysisDescription(ScenarioAnalysisId $id): ?ScenarioAnalysisDescription
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT description FROM %s WHERE scenario_analysis_id = :scenario_analysis_id', Table::SCENARIO_ANALYSIS_LIST),
            ['scenario_analysis_id' => $id->toString()]
        );

        if ($result === false) {
            return null;
        }

        return ScenarioAnalysisDescription::fromString($result['description']);
    }

    public function getScenarioAnalysisVisibility(ScenarioAnalysisId $id): ?Visibility
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT public FROM %s WHERE scenario_analysis_id = :scenario_analysis_id', Table::SCENARIO_ANALYSIS_LIST),
            ['scenario_analysis_id' => $id->toString()]
        );

        if ($result === false) {
            return null;
        }

        return $result['public'] === 1 ? Visibility::public() : Visibility::private();
    }

    public function isBasemodel(ModflowId $modelId): bool
    {
        $results = $this->connection->fetchAssoc(
            sprintf('SELECT count(*) FROM %s WHERE scenario_id = :model_id AND is_base_model', Table::SCENARIO_LIST),
            ['model_id' => $modelId->toString()]
        );

        return $results['count'] > 0;
    }

    public function isScenario(ModflowId $modelId): bool
    {
        $results = $this->connection->fetchAssoc(
            sprintf('SELECT count(*) FROM %s WHERE scenario_id = :model_id AND is_scenario', Table::SCENARIO_LIST),
            ['model_id' => $modelId->toString()]
        );

        return $results['count'] > 0;
    }

    public function findScenarioAnalysesByUserId(UserId $userId): array
    {
        $results = $this->connection->fetchAll(
            sprintf('SELECT scenario_analysis_id as id, user_id, user_name, base_model_id, name, description, created_at, public FROM %s WHERE user_id = :user_id', Table::SCENARIO_ANALYSIS_LIST),
            ['user_id' => $userId->toString()]
        );

        if ($results === false) {
            $results = [];
        }

        return $results;
    }

    public function findPublicScenarioAnalyses(): array
    {
        $results = $this->connection->fetchAll(
            sprintf('SELECT scenario_analysis_id as id, user_id, user_name, base_model_id, name, description, created_at, public FROM %s WHERE public = 1', Table::SCENARIO_ANALYSIS_LIST)
        );

        if ($results === false) {
            $results = [];
        }

        return $results;
    }

    public function isPublic(ScenarioAnalysisId $scenarioAnalysisId): bool
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT count(*) FROM %s WHERE scenario_analysis_id = :scenario_analysis_id AND public = 1', Table::SCENARIO_ANALYSIS_LIST),
            ['scenario_analysis_id' => $scenarioAnalysisId->toString()]
        );

        if ($result === false) {
            return false;
        }

        return $result['count'] === 1;
    }

    /**
     * @param ScenarioAnalysisId $scenarioAnalysisId
     * @return array|null
     * @throws \Exception
     */
    public function findScenarioAnalysisDetailsById(ScenarioAnalysisId $scenarioAnalysisId): ?array
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT scenario_analysis_id as id, user_id, name, description, base_model_id, created_at, public FROM %s WHERE scenario_analysis_id = :scenario_analysis_id', Table::SCENARIO_ANALYSIS_LIST),
            ['scenario_analysis_id' => $scenarioAnalysisId->toString()]
        );

        if ($result === false) {
            return null;
        }

        $result['public'] = (bool)$result['public'];

        $baseModelId = ModflowId::fromString($result['base_model_id']);
        $result['geometry'] = $this->modelFinder->getAreaGeometryByModflowModelId($baseModelId)->toArray();
        $result['grid_size'] = $this->modelFinder->getGridSizeByModflowModelId($baseModelId)->toArray();
        $result['bounding_box'] = $this->modelFinder->getBoundingBoxByModflowModelId($baseModelId)->toArray();


        $result['base_model'] = [
            'id' => $baseModelId->toString(),
            'name' => $this->modelFinder->getModelNameByModelId($baseModelId)->toString(),
            'description' => $this->modelFinder->getModelDescriptionByModelId($baseModelId)->toString(),
            'calculation_id' => $this->modelFinder->getCalculationIdByModelId($baseModelId)->toString()
        ];

        $scenarios = $this->connection->fetchAll(
            sprintf('SELECT scenario_id as id FROM %s WHERE scenario_analysis_id = :scenario_analysis_id AND is_scenario = true ORDER BY created_at', Table::SCENARIO_LIST),
            ['scenario_analysis_id' => $scenarioAnalysisId->toString()]
        );

        if ($scenarios === false) {
            $result['scenarios'] = [];
            return $result;
        }

        foreach ($scenarios as $key => $scenario) {
            $scenarioId = ModflowId::fromString($scenario['id']);
            $scenarios[$key] = [
                'id' => $scenarioId->toString(),
                'name' => $this->modelFinder->getModelNameByModelId($scenarioId)->toString(),
                'description' => $this->modelFinder->getModelDescriptionByModelId($scenarioId)->toString(),
                'calculation_id' => $this->modelFinder->getCalculationIdByModelId($scenarioId)->toString()
            ];
        }

        $result['scenarios'] = $scenarios;

        return $result;
    }

    public function getScenarioNameById(ModflowId $modflowId): ?Name
    {
        return $this->modelFinder->getModelNameByModelId($modflowId);
    }

    public function getScenarioDescriptionById(ModflowId $modflowId): ?Description
    {
        return $this->modelFinder->getModelDescriptionByModelId($modflowId);
    }
}
