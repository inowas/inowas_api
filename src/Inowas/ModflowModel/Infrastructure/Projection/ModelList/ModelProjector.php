<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Infrastructure\Projection\ModelList;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManager;
use Inowas\AppBundle\Model\User;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\ModflowModel\Model\Event\AreaGeometryWasUpdated;
use Inowas\ModflowModel\Model\Event\BoundingBoxWasChanged;
use Inowas\ModflowModel\Model\Event\DescriptionWasChanged;
use Inowas\ModflowModel\Model\Event\GridSizeWasChanged;
use Inowas\ModflowModel\Model\Event\LengthUnitWasUpdated;
use Inowas\ModflowModel\Model\Event\ModflowModelWasCloned;
use Inowas\ModflowModel\Model\Event\NameWasChanged;
use Inowas\ModflowModel\Model\Event\ModflowModelWasCreated;
use Inowas\ModflowModel\Infrastructure\Projection\Table;
use Inowas\ModflowModel\Model\Event\SoilModelIdWasChanged;
use Inowas\ModflowModel\Model\Event\TimeUnitWasUpdated;

class ModelProjector extends AbstractDoctrineConnectionProjector
{
    /** @var  EntityManager $entityManager */
    protected $entityManager;

    public function __construct(Connection $connection, EntityManager $entityManager)
    {
        parent::__construct($connection);
        $this->entityManager = $entityManager;

        $this->schema = new Schema();
        $table = $this->schema->createTable(Table::MODFLOWMODELS_LIST);
        $table->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
        $table->addColumn('model_id', 'string', ['length' => 36]);
        $table->addColumn('user_id', 'string', ['length' => 36]);
        $table->addColumn('soilmodel_id', 'string', ['length' => 36, 'notnull' => false]);
        $table->addColumn('user_name', 'string', ['length' => 255]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('description', 'string', ['length' => 255]);
        $table->addColumn('area', 'text', ['notnull' => false]);
        $table->addColumn('grid_size', 'text', ['notnull' => false]);
        $table->addColumn('bounding_box', 'text', ['notnull' => false]);
        $table->addColumn('time_unit', 'integer');
        $table->addColumn('length_unit', 'integer');
        $table->addColumn('created_at', 'string', ['length' => 255, 'notnull' => false]);
        $table->addColumn('public', 'boolean');
        $table->setPrimaryKey(['id']);
        $table->addIndex(array('model_id'));
    }

    public function onAreaGeometryWasUpdated(AreaGeometryWasUpdated $event): void
    {
        $this->connection->update(Table::MODFLOWMODELS_LIST, array(
            'area' => $event->geometry()->toJson(),
        ),
            array('model_id' => $event->modelId()->toString())
        );
    }

    public function onBoundingBoxWasChanged(BoundingBoxWasChanged $event): void
    {
        $this->connection->update(Table::MODFLOWMODELS_LIST, array(
            'bounding_box' => json_encode($event->boundingBox()),
        ),
            array('model_id' => $event->modflowId()->toString())
        );
    }

    public function onDescriptionWasChanged(DescriptionWasChanged $event): void
    {
        $this->connection->update(Table::MODFLOWMODELS_LIST,
            array('description' => $event->description()->toString()),
            array('model_id' => $event->modflowModelId()->toString())
        );
    }

    public function onGridSizeWasChanged(GridSizeWasChanged $event): void
    {
        $this->connection->update(Table::MODFLOWMODELS_LIST, array(
            'grid_size' => json_encode($event->gridSize())
        ),
            array('model_id' => $event->modflowId()->toString())
        );
    }

    public function onModflowModelWasCreated(ModflowModelWasCreated $event): void
    {
        $this->connection->insert(Table::MODFLOWMODELS_LIST, array(
            'model_id' => $event->modelId()->toString(),
            'user_id' => $event->userId()->toString(),
            'user_name' => $this->getUserNameByUserId($event->userId()->toString()),
            'soilmodel_id' => $event->soilmodelId()->toString(),
            'name' => '',
            'description' => '',
            'area' => $event->area()->geometry()->toJson(),
            'grid_size' => json_encode($event->gridSize()),
            'bounding_box' => json_encode($event->boundingBox()),
            'time_unit' => $event->timeUnit()->toInt(),
            'length_unit' => $event->lengthUnit()->toInt(),
            'created_at' => date_format($event->createdAt(), DATE_ATOM),
            'public' => true
        ));
    }

    public function onModflowModelWasCloned(ModflowModelWasCloned $event): void
    {

        $rows = $this->connection->fetchAll(
            sprintf('SELECT * FROM %s WHERE model_id = :model_id', Table::MODFLOWMODELS_LIST),
            ['model_id' => $event->baseModelId()->toString()]
        );

        foreach ($rows as $row){
            $this->connection->insert(Table::MODFLOWMODELS_LIST, array(
                'model_id' => $event->modelId()->toString(),
                'user_id' => $event->userId()->toString(),
                'user_name' => $this->getUserNameByUserId($event->userId()->toString()),
                'soilmodel_id' => $event->soilmodelId()->toString(),
                'name' => $row['name'],
                'description' => $row['description'],
                'area' => $row['area'],
                'grid_size' => $row['grid_size'],
                'bounding_box' => $row['bounding_box'],
                'time_unit' => $row['time_unit'],
                'length_unit' => $row['length_unit'],
                'created_at' => date_format($event->createdAt(), DATE_ATOM),
                'public' => true
            ));
        }
    }

    public function onLengthUnitWasUpdated(LengthUnitWasUpdated $event): void
    {
        $this->connection->update(Table::MODFLOWMODELS_LIST,
            array('length_unit' => $event->lengthUnit()->toInt()),
            array('model_id' => $event->modflowId()->toString())
        );
    }

    public function onTimeUnitWasUpdated(TimeUnitWasUpdated $event): void
    {
        $this->connection->update(Table::MODFLOWMODELS_LIST,
            array('time_unit' => $event->timeUnit()->toInt()),
            array('model_id' => $event->modflowId()->toString())
        );
    }

    public function onNameWasChanged(NameWasChanged $event): void
    {
        $this->connection->update(Table::MODFLOWMODELS_LIST,
            array('name' => $event->name()->toString()),
            array('model_id' => $event->modflowId()->toString())
        );
    }

    public function onSoilModelIdWasChanged(SoilModelIdWasChanged $event): void
    {
        $this->connection->update(Table::MODFLOWMODELS_LIST,
            array('soilmodel_id' => $event->soilModelId()->toString()),
            array('model_id' => $event->modflowModelId()->toString())
        );
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
