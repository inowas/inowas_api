<?php

declare(strict_types=1);

namespace Inowas\ScenarioAnalysis\Infrastructure\Projection;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManager;
use Inowas\AppBundle\Model\User;
use Inowas\Common\Id\UserId;
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

    /** @var  EntityManager */
    private $entityManager;

    public function __construct(Connection $connection, ModelFinder $modelFinder, EntityManager $entityManager)
    {

        $this->entityManager = $entityManager;
        $this->modelFinder = $modelFinder;

        parent::__construct($connection);

        $this->schema = new Schema();
        $table = $this->schema->createTable(Table::SCENARIO_ANALYSIS_LIST);
        $table->addColumn('scenario_analysis_id', 'string', ['length' => 36]);
        $table->addColumn('user_id', 'string', ['length' => 36]);
        $table->addColumn('user_name', 'string', ['length' => 255]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('description', 'string', ['length' => 255]);
        $table->addColumn('area', 'text');
        $table->addColumn('grid_size', 'text');
        $table->addColumn('bounding_box', 'text');
        $table->addColumn('base_model', 'text');
        $table->addColumn('scenarios', 'text');
        $table->addColumn('created_at', 'string', ['length' => 255, 'notnull' => false]);
        $table->addColumn('public', 'boolean');
        $table->setPrimaryKey(['scenario_analysis_id']);
        $table->addIndex(array('base_model_id'));
    }

    public function onScenarioAnalysisWasCreated(ScenarioAnalysisWasCreated $event)
    {
        $areaGeometry = $this->modelFinder->getAreaPolygonByModflowModelId($event->baseModelId());
        $boundingBox = $this->modelFinder->getBoundingBoxByModflowModelId($event->baseModelId());
        $gridSize = $this->modelFinder->getGridSizeByModflowModelId($event->baseModelId());
        $baseModelName = $this->modelFinder->getModelNameByModelId($event->baseModelId());
        $baseModelDescription = $this->modelFinder->getModelDescriptionByModelId($event->baseModelId());


        $baseModel = array(
            'id' => $event->baseModelId()->toString(),
            'name' => $baseModelName->toString(),
            'description' => $baseModelDescription->toString()
        );

        $this->connection->insert(Table::SCENARIO_ANALYSIS_LIST, array(
            'scenario_analysis_id' => $event->scenarioAnalysisId()->toString(),
            'user_id' => $event->userId()->toString(),
            'user_name' => $this->getUserNameByUserId($event->userId()),
            'name' => '',
            'description' => '',
            'area' => $areaGeometry->toJson(),
            'grid_size' => json_encode($gridSize),
            'bounding_box' => json_encode($boundingBox),
            'base_model' => json_encode($baseModel),
            'scenarios' => json_encode([]),
            'created_at' => date_format($event->createdAt(), DATE_ATOM),
            'public' => true
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

        if ($result === false){
            return null;
        }

        $scenarios = json_decode($result['scenarios']);
        $newScenario = array(
            'id' => $event->scenarioId()->toString(),
            'name' => "",
            'description' => ""
        );

        $scenarios[] = $newScenario;
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

        foreach ($scenarios as $key => $scenario) {
            if ($scenario['id'] === $event->scenarioId()->toString()){
                unset($scenarios[$key]);
                array_values($scenarios);
            }
        }

        $this->connection->update(Table::SCENARIO_ANALYSIS_LIST,
            array('scenarios' => json_encode($scenarios)),
            array('scenario_analysis_id' => $event->scenarioAnalysisId()->toString())
        );
    }

    private function getUserNameByUserId(UserId $id): string
    {
        $username = '';
        $user = $this->entityManager->getRepository('InowasAppBundle:User')->findOneBy(array('id' => $id->toString()));
        if ($user instanceof User){
            $username = $user->getName();
        }

        return $username;
    }
}
