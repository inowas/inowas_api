<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Handler;

use Inowas\Common\Soilmodel\SoilmodelId;
use Inowas\GeoTools\Service\GeoTools;
use Inowas\ModflowModel\Model\Command\CreateModflowModel;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;
use Inowas\Soilmodel\Model\Command\CreateSoilmodel;
use Prooph\ServiceBus\CommandBus;

final class CreateModflowModelHandler
{

    /** @var  ModflowModelList */
    private $modelList;

    /** @var  GeoTools */
    private $geoTools;

    /** @var  CommandBus */
    private $commandBus;

    public function __construct(ModflowModelList $modelList, GeoTools $geoTools, CommandBus $commandBus)
    {
        $this->modelList = $modelList;
        $this->geoTools = $geoTools;
        $this->commandBus = $commandBus;
    }

    public function __invoke(CreateModflowModel $command)
    {
        $boundingBox = $this->geoTools->getBoundingBoxFromPolygon($command->area()->geometry());

        $soilmodelId = SoilmodelId::generate();
        $this->commandBus->dispatch(CreateSoilmodel::byUserWithModelId(
            $command->userId(),
            $soilmodelId
        ));

        $modflowModel = ModflowModelAggregate::create(
            $command->modflowModelId(),
            $command->userId(),
            $soilmodelId,
            $command->area(),
            $command->gridSize(),
            $boundingBox,
            $command->lengthUnit(),
            $command->timeUnit()
        );

        $this->modelList->add($modflowModel);
    }
}
