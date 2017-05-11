<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Infrastructure\Projection\ModelList;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManager;
use Inowas\AppBundle\Model\User;
use Inowas\Common\Boundaries\Area;
use Inowas\Common\Boundaries\BoundaryName;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\GeoTools\Service\GeoTools;
use Inowas\ModflowModel\Model\Event\AreaGeometryWasUpdated;
use Inowas\ModflowModel\Model\Event\BoundingBoxWasChanged;
use Inowas\ModflowModel\Model\Event\DescriptionWasChanged;
use Inowas\ModflowModel\Model\Event\GridSizeWasChanged;
use Inowas\ModflowModel\Model\Event\NameWasChanged;
use Inowas\ModflowModel\Model\Event\ModflowModelWasCreated;
use Inowas\ModflowModel\Infrastructure\Projection\Table;

class ModelProjector extends AbstractDoctrineConnectionProjector
{
    /** @var  EntityManager $entityManager */
    protected $entityManager;

    /** @var  GeoTools */
    protected $geoTools;

    public function __construct(Connection $connection, EntityManager $entityManager, GeoTools $geoTools)
    {
        parent::__construct($connection);
        $this->entityManager = $entityManager;
        $this->geoTools = $geoTools;

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
        $table->addColumn('active_cells', 'text', ['notnull' => false]);
        $table->addColumn('created_at', 'string', ['length' => 255, 'notnull' => false]);
        $table->addColumn('public', 'boolean');
        $table->setPrimaryKey(['id']);
        $table->addIndex(array('model_id'));
    }

    public function onModflowModelWasCreated(ModflowModelWasCreated $event): void
    {

        $area = $event->area();
        $gridSize = $event->gridSize();
        $boundingBox = $event->boundingBox();
        $activeCells = $this->geoTools->calculateActiveCellsFromArea($area, $boundingBox, $gridSize);

        $this->connection->insert(Table::MODEL_DETAILS, array(
            'model_id' => $event->modelId()->toString(),
            'user_id' => $event->userId()->toString(),
            'user_name' => $this->getUserNameByUserId($event->userId()->toString()),
            'name' => '',
            'description' => '',
            'area' => $event->area()->geometry()->toJson(),
            'grid_size' => json_encode($event->gridSize()),
            'bounding_box' => json_encode($event->boundingBox()),
            'active_cells' => json_encode($activeCells->toArray()),
            'created_at' => date_format($event->createdAt(), DATE_ATOM),
            'public' => true
        ));
    }

    public function onAreaGeometryWasUpdated(AreaGeometryWasUpdated $event): void
    {
        // This is a dirty hack
        // To calculate active cells from Area, we nee an area object
        // Only the geometry is important, so wie generate a new one with a random ID and empty Name
        $area = Area::create(BoundaryId::generate(), BoundaryName::fromString(''), $event->geometry());
        $gridSize = $this->findGridSizeByModflowModelId($event->modelId());
        $boundingBox = $this->findBoundingBoxByModflowModelId($event->modelId());
        $activeCells = $this->geoTools->calculateActiveCellsFromArea($area, $boundingBox, $gridSize);

        $this->connection->update(Table::MODEL_DETAILS, array(
            'area' => $event->geometry()->toJson(),
            'active_cells' => json_encode($activeCells->toArray()),
        ),
            array('model_id' => $event->modelId()->toString())
        );
    }

    public function onNameWasChanged(NameWasChanged $event): void
    {
        $this->connection->update(Table::MODEL_DETAILS,
            array('name' => $event->name()->toString()),
            array('model_id' => $event->modflowId()->toString())
        );
    }

    public function onDescriptionWasChanged(DescriptionWasChanged $event): void
    {
        $this->connection->update(Table::MODEL_DETAILS,
            array('description' => $event->description()->toString()),
            array('model_id' => $event->modflowModelId()->toString())
        );
    }

    public function onBoundingBoxWasChanged(BoundingBoxWasChanged $event): void
    {
        $area = $this->findAreaByModflowModelId($event->modflowId());
        $gridSize = $this->findGridSizeByModflowModelId($event->modflowId());
        $boundingBox = $event->boundingBox();
        $activeCells = $this->geoTools->calculateActiveCellsFromArea($area, $boundingBox, $gridSize);

        $this->connection->update(Table::MODEL_DETAILS, array(
            'bounding_box' => json_encode($event->boundingBox()),
            'active_cells' => json_encode($activeCells->toArray())
        ),
            array('model_id' => $event->modflowId()->toString())
        );
    }

    public function onGridSizeWasChanged(GridSizeWasChanged $event): void
    {
        $area = $this->findAreaByModflowModelId($event->modflowId());
        $gridSize = $event->gridSize();
        $boundingBox = $this->findBoundingBoxByModflowModelId($event->modflowId());
        $activeCells = $this->geoTools->calculateActiveCellsFromArea($area, $boundingBox, $gridSize);

        $this->connection->update(Table::MODEL_DETAILS, array(
            'grid_size' => json_encode($event->gridSize()),
            'active_cells' => json_encode($activeCells->toArray())
        ),
            array('model_id' => $event->modflowId()->toString())
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

    private function findAreaByModflowModelId(ModflowId $modelId): Area
    {
        $result =  $this->connection->fetchAssoc(
            sprintf('SELECT area FROM %s WHERE model_id = :model_id', Table::MODEL_DETAILS),
            ['model_id' => $modelId->toString()]
        );

        $areaPolygon = Geometry::fromJson($result['area'])->value();
        return Area::create(BoundaryId::generate(), BoundaryName::fromString(''), $areaPolygon);
    }

    private function findBoundingBoxByModflowModelId(ModflowId $modelId): ?BoundingBox
    {
        $result =  $this->connection->fetchAssoc(
            sprintf('SELECT bounding_box FROM %s WHERE model_id = :model_id', Table::MODEL_DETAILS),
            ['model_id' => $modelId->toString()]
        );

        if ($result === false){
            return null;
        }

        return BoundingBox::fromArray((array)json_decode($result['bounding_box']));
    }

    private function findGridSizeByModflowModelId(ModflowId $modelId): ?GridSize
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT grid_size FROM %s WHERE model_id = :model_id', Table::MODEL_DETAILS),
            ['model_id' => $modelId->toString()]
        );

        if ($result === false){
            return null;
        }

        return GridSize::fromArray((array)json_decode($result['grid_size']));
    }
}
