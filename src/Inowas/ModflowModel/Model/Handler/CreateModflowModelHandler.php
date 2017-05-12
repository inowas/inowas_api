<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Handler;

use Inowas\GeoTools\Service\GeoTools;
use Inowas\ModflowModel\Model\Command\CreateModflowModel;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;

final class CreateModflowModelHandler
{

    /** @var  ModflowModelList */
    private $modelList;

    /** @var  GeoTools */
    private $geoTools;

    public function __construct(ModflowModelList $modelList, GeoTools $geoTools)
    {
        $this->geoTools = $geoTools;
        $this->modelList = $modelList;
    }

    public function __invoke(CreateModflowModel $command)
    {
        $boundingBox = $this->geoTools->getBoundingBoxFromPolygon($command->area()->geometry());

        $modflowModel = ModflowModelAggregate::create(
            $command->modflowModelId(),
            $command->userId(),
            $command->area(),
            $command->gridSize(),
            $boundingBox,
            $command->lengthUnit(),
            $command->timeUnit()
        );

        $this->modelList->add($modflowModel);
    }
}
