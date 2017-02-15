<?php

namespace Inowas\Modflow\Projection;

use Inowas\Modflow\Model\Event\ModflowModelBoundingBoxWasChanged;
use Inowas\Modflow\Model\Event\ModflowModelDescriptionWasChanged;
use Inowas\Modflow\Model\Event\ModflowModelGridSizeWasChanged;
use Inowas\Modflow\Model\Event\ModflowModelNameWasChanged;
use Inowas\Modflow\Model\Event\ModflowModelWasCreated;
use Inowas\Modflow\Model\Event\ModflowScenarioWasAdded;
use Inowas\ModflowBundle\Model\BoundingBox;
use Inowas\ModflowBundle\Model\GridSize;
use Inowas\ModflowBundle\Model\ModflowModel;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class ModelScenariosProjector
{
    private $cache;

    /** @var array */
    private $models = [];

    public function __construct() {
        $this->cache = new FilesystemAdapter();
        $this->cache->deleteItem('inowas.modflow.models');
    }

    public function onModflowModelWasCreated(ModflowModelWasCreated $event)
    {
        $this->models[$event->modflowModelId()->toString()] = ModflowModel::createWithModflowId($event->modflowModelId());
    }

    public function onModflowModelNameWasChanged(ModflowModelNameWasChanged $event)
    {
        /** @var ModflowModel $model */
        $model = $this->models[$event->modflowId()->toString()];
        $model->setName($event->name()->toString());
    }

    public function onModflowModelDescriptionWasChanged(ModflowModelDescriptionWasChanged $event)
    {
        /** @var ModflowModel $model */
        $model = $this->models[$event->modflowModelId()->toString()];
        $model->setDescription($event->description()->toString());
    }

    public function onModflowModelGridSizeWasChanged(ModflowModelGridSizeWasChanged $event)
    {
        /** @var ModflowModel $model */
        $model = $this->models[$event->modflowModelId()->toString()];
        $model->setGridSize(new GridSize($event->gridSize()->nX(), $event->gridSize()->nY()));
    }

    public function onModflowModelBoundingBoxWasChanged(ModflowModelBoundingBoxWasChanged $event)
    {
        /** @var ModflowModel $model */
        $model = $this->models[$event->modflowModelId()->toString()];
        $model->setBoundingBox(
            new BoundingBox(
                $event->boundingBox()->xMin(),
                $event->boundingBox()->xMax(),
                $event->boundingBox()->yMin(),
                $event->boundingBox()->yMin(),
                $event->boundingBox()->srid()
            )
        );
    }

    public function onModflowScenarioWasAdded(ModflowScenarioWasAdded $event): void
    {
        $scenario = unserialize(serialize($this->models[$event->baseModelId()->toString()]));
        $this->models[$event->scenarioId()->toString()] = $scenario;
    }

    public function getData(): array
    {
        return $this->models;
    }
}
