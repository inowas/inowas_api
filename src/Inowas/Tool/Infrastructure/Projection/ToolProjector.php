<?php

declare(strict_types=1);

namespace Inowas\Tool\Infrastructure\Projection;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManager;
use Inowas\AppBundle\Model\User;
use Inowas\Common\Id\UserId;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\ModflowModel\Model\Event\ModflowModelWasCreated;
use Inowas\ScenarioAnalysis\Model\Event\ScenarioAnalysisDescriptionWasChanged;
use Inowas\ScenarioAnalysis\Model\Event\ScenarioAnalysisNameWasChanged;
use Inowas\ScenarioAnalysis\Model\Event\ScenarioAnalysisWasCloned;
use Inowas\ScenarioAnalysis\Model\Event\ScenarioAnalysisWasCreated;
use Inowas\ScenarioAnalysis\Model\Event\ScenarioAnalysisWasDeleted;

class ToolProjector extends AbstractDoctrineConnectionProjector
{

    /** @var  EntityManager */
    private $entityManager;

    public function __construct(Connection $connection, EntityManager $entityManager)
    {

        $this->entityManager = $entityManager;

        parent::__construct($connection);

        $schema = new Schema();
        $table = $schema->createTable(Table::TOOL_LIST);
        $table->addColumn('id', 'string', ['length' => 36]);
        $table->addColumn('name', 'string', ['length' => 255, 'default' => '']);
        $table->addColumn('description', 'string', ['length' => 255, 'default' => '']);
        $table->addColumn('project', 'string', ['length' => 255, 'default' => '']);
        $table->addColumn('application', 'string', ['length' => 255, 'default' => '']);
        $table->addColumn('tool', 'string', ['length' => 255, 'default' => '']);
        $table->addColumn('created_at', 'string', ['length' => 255, 'notnull' => false]);
        $table->addColumn('user_id', 'string', ['length' => 36]);
        $table->addColumn('user_name', 'string', ['length' => 255]);
        $table->addColumn('public', 'boolean');
        $table->setPrimaryKey(['id']);
        $this->addSchema($schema);
    }

    public function onModflowModelWasCreated(ModflowModelWasCreated $event): void
    {
        $this->connection->insert(Table::TOOL_LIST, array(
            'id' => $event->modelId()->toString(),
            'application' => '',
            'project' => '',
            'tool' => 'T03',
            'user_id' => $event->userId()->toString(),
            'user_name' => $this->getUserNameByUserId($event->userId()),
            'created_at' => date_format($event->createdAt(), DATE_ATOM),
            'public' => true
        ));
    }

    public function onScenarioAnalysisWasCreated(ScenarioAnalysisWasCreated $event): void
    {
        $this->connection->insert(Table::TOOL_LIST, array(
            'id' => $event->scenarioAnalysisId()->toString(),
            'name' => $event->name()->toString(),
            'description' => $event->description()->toString(),
            'application' => '',
            'project' => '',
            'tool' => 'T07',
            'user_id' => $event->userId()->toString(),
            'user_name' => $this->getUserNameByUserId($event->userId()),
            'created_at' => date_format($event->createdAt(), DATE_ATOM),
            'public' => true
        ));
    }

    public function onScenarioAnalysisWasCloned(ScenarioAnalysisWasCloned $event): void
    {
        $this->connection->insert(Table::TOOL_LIST, array(
            'id' => $event->scenarioAnalysisId()->toString(),
            'name' => $event->name()->toString(),
            'description' => $event->description()->toString(),
            'application' => '',
            'project' => '',
            'tool' => 'T07',
            'user_id' => $event->userId()->toString(),
            'user_name' => $this->getUserNameByUserId($event->userId()),
            'created_at' => date_format($event->createdAt(), DATE_ATOM),
            'public' => true
        ));
    }

    public function onScenarioAnalysisWasDeleted(ScenarioAnalysisWasDeleted $event): void
    {
        $this->connection->delete(
            Table::TOOL_LIST,
            array('id' => $event->scenarioAnalysisId()->toString(), 'user_id' => $event->userId()->toString())
        );
    }

    public function onScenarioAnalysisNameWasChanged(ScenarioAnalysisNameWasChanged $event): void
    {
        $this->connection->update(Table::TOOL_LIST,
            array('name' => $event->name()->toString()),
            array('id' => $event->scenarioAnalysisId()->toString())
        );
    }

    public function onScenarioAnalysisDescriptionWasChanged(ScenarioAnalysisDescriptionWasChanged $event):void
    {
        $this->connection->update(Table::TOOL_LIST,
            array('description' => $event->description()->toString()),
            array('id' => $event->scenarioAnalysisId()->toString())
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
