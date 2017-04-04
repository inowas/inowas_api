<?php

declare(strict_types=1);

namespace Inowas\Modflow\Projection\ModelScenarioList;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManager;
use Inowas\AppBundle\Model\User;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\Modflow\Model\Event\ActiveCellsWereUpdated;
use Inowas\Modflow\Model\Event\BoundaryWasAdded;
use Inowas\Modflow\Model\Event\BoundaryWasRemoved;
use Inowas\Modflow\Model\Event\ModflowModelBoundaryWasUpdated;
use Inowas\Modflow\Model\Event\ModflowModelBoundingBoxWasChanged;
use Inowas\Modflow\Model\Event\ModflowModelDescriptionWasChanged;
use Inowas\Modflow\Model\Event\ModflowModelGridSizeWasChanged;
use Inowas\Modflow\Model\Event\ModflowModelNameWasChanged;
use Inowas\Modflow\Model\Event\ModflowModelWasCreated;
use Inowas\Modflow\Model\Event\ModflowScenarioWasAdded;
use Inowas\Modflow\Model\Event\ModflowScenarioWasRemoved;
use Inowas\Modflow\Projection\Table;

class ModelDetailsProjector extends AbstractDoctrineConnectionProjector
{
    /** @var  EntityManager $entityManager */
    protected $entityManager;

    public function __construct(Connection $connection, EntityManager $entityManager)
    {

        parent::__construct($connection);
        $this->entityManager = $entityManager;

        $this->schema = new Schema();
        $table = $this->schema->createTable(Table::MODEL_DETAILS);
        $table->addColumn('id', 'integer', array("unsigned" => true, "autoincrement" => true));
        $table->addColumn('model_id', 'string', ['length' => 36]);
        $table->addColumn('user_id', 'string', ['length' => 36]);
        $table->addColumn('user_name', 'string', ['length' => 255]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('description', 'string', ['length' => 255]);
        $table->addColumn('area', 'text', ['notnull' => false]);
        $table->addColumn('grid_size', 'text', ['notnull' => false]);
        $table->addColumn('bounding_box', 'text', ['notnull' => false]);
        $table->addColumn('created_at', 'string', ['length' => 255, 'notnull' => false]);
        $table->addColumn('nr_of_scenarios', 'integer');
        $table->addColumn('public', 'boolean');
        $table->setPrimaryKey(['id']);
        $table->addIndex(array('model_id'));
    }

    public function onModflowModelWasCreated(ModflowModelWasCreated $event): void
    {
        $this->connection->insert(Table::MODEL_DETAILS, array(
            'model_id' => $event->modflowModelId()->toString(),
            'user_id' => $event->userId()->toString(),
            'user_name' => $this->getUserNameByUserId($event->userId()->toString()),
            'name' => '',
            'description' => '',
            'created_at' => date_format($event->createdAt(), DATE_ATOM),
            'nr_of_scenarios' => 0,
            'public' => true
        ));
    }

    public function onModflowModelNameWasChanged(ModflowModelNameWasChanged $event): void
    {
        $this->connection->update(Table::MODEL_DETAILS,
            array('name' => $event->name()->toString()),
            array('model_id' => $event->modflowId()->toString())
        );
    }

    public function onModflowModelDescriptionWasChanged(ModflowModelDescriptionWasChanged $event): void
    {
        $this->connection->update(Table::MODEL_DETAILS,
            array('description' => $event->description()->toString()),
            array('model_id' => $event->modflowModelId()->toString())
        );
    }

    public function onBoundaryWasAdded(BoundaryWasAdded $event): void
    {
        $boundary = $event->boundary();
        if ($boundary->type() == 'area') {
            $this->connection->update(Table::MODEL_DETAILS,
                array('area' => json_encode($boundary->geometry()->toJson())),
                array('model_id' => $event->modflowId()->toString())
            );
        }
    }

    public function onModflowModelBoundaryWasUpdated(ModflowModelBoundaryWasUpdated $event): void
    {
        $boundary = $event->boundary();
        if ($boundary->type() == 'area') {
            $this->connection->update(Table::MODEL_DETAILS,
                array('area' => json_encode($boundary->geometry()->toJson())),
                array('model_id' => $event->modflowId()->toString())
            );
        }
    }

    public function onModflowModelBoundingBoxWasChanged(ModflowModelBoundingBoxWasChanged $event): void
    {
        $this->connection->update(Table::MODEL_DETAILS,
            array('bounding_box' => json_encode($event->boundingBox())),
            array('model_id' => $event->modflowModelId()->toString())
        );
    }

    public function onModflowModelGridSizeWasChanged(ModflowModelGridSizeWasChanged $event): void
    {
        $this->connection->update(Table::MODEL_DETAILS,
            array('grid_size' => json_encode($event->gridSize())),
            array('model_id' => $event->modflowModelId()->toString())
        );
    }

    public function onModflowScenarioWasAdded(ModflowScenarioWasAdded $event): void
    {
        $rows = $this->connection->fetchAll(
            sprintf('SELECT id, nr_of_scenarios from %s WHERE model_id = :model_id', Table::MODEL_DETAILS),
            ['model_id' => $event->baseModelId()->toString()]
        );

        foreach ($rows as $row){
            $id = $row['id'];
            $nrOfScenarios = $row['nr_of_scenarios'];

            $this->connection->update(Table::MODEL_DETAILS,
                array('nr_of_scenarios' => ++$nrOfScenarios),
                array('id' => $id)
            );
        }
    }

    public function onModflowScenarioWasRemoved(ModflowScenarioWasRemoved $event): void
    {
        $rows = $this->connection->fetchAll(
            sprintf('SELECT id, nr_of_scenarios from %s WHERE model_id = :model_id', Table::MODEL_DETAILS),
            ['model_id' => $event->baseModelId()->toString()]
        );

        foreach ($rows as $row){
            $id = $row['id'];
            $nrOfScenarios = $row['nr_of_scenarios'];

            $this->connection->update(Table::MODEL_DETAILS,
                array('nr_of_scenarios' => --$nrOfScenarios),
                array('id' => $id)
            );
        }
    }

    private function getUserNameByUserId(string $id): string
    {
        $username = '';
        $user = $this->entityManager->getRepository('InowasAppBundle:User')->findOneBy(array('id' => $id));
        if ($user instanceof User){
            $username = $user->getName();
        }

        return $username;
    }
}
