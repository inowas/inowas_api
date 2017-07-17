<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Handler;

use Inowas\GeoTools\Service\GeoTools;
use Inowas\ModflowModel\Model\Command\CreateModflowModel;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;

final class CreateModflowModelHandler
{

    /** @var  GeoTools */
    private $geoTools;

    /** @var  ModflowModelList */
    private $modelList;


    public function __construct(ModflowModelList $modelList, GeoTools $geoTools)
    {
        $this->geoTools = $geoTools;
        $this->modelList = $modelList;
    }

    public function __invoke(CreateModflowModel $command)
    {
        $boundingBox = $this->geoTools->getBoundingBoxFromPolygon($command->polygon());

        $modflowModel = ModflowModelAggregate::create(
            $command->modelId(),
            $command->userId(),
            $command->polygon(),
            $command->gridSize(),
            $boundingBox,
            $command->soilmodelId()
        );

        $modflowModel->changeName($command->userId(), $command->name());
        $modflowModel->changeDescription($command->userId(), $command->description());
        $modflowModel->updateTimeUnit($command->userId(), $command->timeUnit());
        $modflowModel->updateLengthUnit($command->userId(), $command->lengthUnit());

        $this->modelList->save($modflowModel);
    }
}
